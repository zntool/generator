<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ParameterGenerator;
use ZnCore\Base\Legacy\Code\entities\ClassEntity;
use ZnCore\Base\Legacy\Code\entities\ClassUseEntity;
use ZnCore\Base\Legacy\Code\entities\ClassVariableEntity;
use ZnCore\Base\Legacy\Code\entities\InterfaceEntity;
use ZnCore\Base\Legacy\Code\enums\AccessEnum;
use ZnCore\Base\Libs\Store\StoreFile;
use ZnCore\Domain\Constraints\Enum;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnTool\Generator\Domain\Dto\BuildDto;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\PropertyValueGenerator;
use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;
use ZnUser\Notify\Domain\Enums\NotifyStatusEnum;

class EntityScenario extends BaseScenario
{

    public function init()
    {
        $this->attributes = $this->buildDto->attributes;
    }

    public function typeName()
    {
        return 'Entity';
    }

    public function classDir()
    {
        return 'Entities';
    }

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $classGenerator->setName($className);

        $implementedInterfaces = [];
        $fileGenerator->setUse('Symfony\Component\Validator\Constraints', 'Assert');
//        $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface');
//        $implementedInterfaces[] = 'ValidateEntityInterface';

        $fileGenerator->setUse('Symfony\Component\Validator\Mapping\ClassMetadata');

        $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\ValidateEntityByMetadataInterface');
        $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\UniqueInterface');
        $implementedInterfaces[] = 'ValidateEntityByMetadataInterface';
        $implementedInterfaces[] = 'UniqueInterface';

        if(in_array('id', $this->attributes)) {
            $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\EntityIdInterface');
            $implementedInterfaces[] = 'EntityIdInterface';
        }

        $classGenerator->setImplementedInterfaces($implementedInterfaces);

        if(in_array('created_at', $this->attributes)) {
            $fileGenerator->setUse(\DateTime::class);
            $constructBody = '$this->createdAt = new DateTime();';
            $classGenerator->addMethod('__construct', [], [], $constructBody);
        }

        $validateBody = $this->generateValidationRulesBody($this->attributes, $fileGenerator);
        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('metadata');
        $parameterGenerator->setType('Symfony\Component\Validator\Mapping\ClassMetadata');
        $classGenerator->addMethod('loadValidatorMetadata', [$parameterGenerator], [MethodGenerator::FLAG_STATIC], $validateBody);

        $methodGenerator = $this->generateUniqueMethod();
        $classGenerator->addMethodFromGenerator($methodGenerator);
        //$classGenerator->addMethod('unique', [$parameterGenerator], [MethodGenerator::FLAG_STATIC], $validateBody);


        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);

                $propertyGenerator = new PropertyGenerator($attributeName, null, PropertyGenerator::FLAG_PRIVATE);
//                $propertyGenerator->setDefaultValue();
                $classGenerator->addPropertyFromGenerator($propertyGenerator);

                $setterMethodGenerator = $this->generateSetter($attributeName);
                $classGenerator->addMethodFromGenerator($setterMethodGenerator);

                $getterMethodGenerator = $this->generateGetter($attributeName);
                $classGenerator->addMethodFromGenerator($getterMethodGenerator);
            }
        }

        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);
        $fileGenerator->setSourceDirty(false);

        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $phpCode);

        $this->updateContainerConfig($fileGenerator);
    }

    private function updateContainerConfig(FileGenerator $fileGenerator) {
        $fullClassName = $this->getFullClassName();
        $containerFileName = PackageHelper::pathByNamespace($this->domainNamespace) . '/config/container.php';
        $storeFile = new StoreFile($containerFileName);
        $containerConfig = $storeFile->load();

        $repoGen = new RepositoryScenario();
        $repoGen->buildDto = $this->buildDto;
        $repoGen->domainNamespace = $this->domainNamespace;
        $repoGen->name = $this->name;

        $containerConfig['entities'][$fullClassName] = $repoGen->getInterfaceFullName();
        $storeFile->save($containerConfig);
    }

    private function generateUniqueMethod(): MethodGenerator {
        $methodBody = "return [];";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('unique');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('array');
        return $methodGenerator;
    }


    private function generateValidationRulesBody(array $attributes, FileGenerator $fileGenerator): string {
        $validationRules = [];
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);
                if($attribute !== 'id') {
                    $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\NotBlank());";
                }

                $isInt = FieldRenderHelper::isMatchSuffix($attribute, '_id');
                if($isInt) {
                    $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\Positive());";
                }

                $isStatus = $attribute == 'status_id';
                if($isStatus) {
                    $fileGenerator->setUse(\ZnCore\Base\Enums\StatusEnum::class);
                    $fileGenerator->setUse(\ZnCore\Domain\Constraints\Enum::class);
                    $validationRules[] =
"\$metadata->addPropertyConstraint('$attributeName', new Enum([
    'class' => StatusEnum::class,
]));";
                }

                $isBoolean = FieldRenderHelper::isMatchPrefix($attribute, 'is_');
                if($isBoolean) {
                    $fileGenerator->setUse(\ZnCore\Domain\Constraints\Boolean::class);
                    $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Boolean());";
                }

                $isCount = FieldRenderHelper::isMatchSuffix($attribute, '_count') || $attribute == 'size';
                if($isCount) {
                    $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\PositiveOrZero());";
                }

            }
        }
        $validateBody = implode(PHP_EOL, $validationRules);
        return $validateBody;
    }

    private function generateSetter(string $attributeName): MethodGenerator {
        $methodBody = '$this->' . $attributeName . ' = $value;';
        $methodName = 'set' . Inflector::camelize($attributeName);
        $methodGenerator = new MethodGenerator($methodName, ['value']);
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('void');
        return $methodGenerator;
    }

    private function generateGetter(string $attributeName): MethodGenerator {
        $methodBody = 'return $this->' . $attributeName . ';';
        $methodName = 'get' . Inflector::camelize($attributeName);
        $methodGenerator = new MethodGenerator($methodName);
        $methodGenerator->setBody($methodBody);
        //$methodGenerator->setReturnType('void');
        return $methodGenerator;
    }

}
