<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ParameterGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
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
use ZnTool\Generator\Domain\Libs\ConstraintCodeGenerator;
use ZnTool\Package\Domain\Helpers\PackageHelper;
use ZnUser\Notify\Domain\Enums\NotifyStatusEnum;

class EntityScenario extends BaseEntityScenario
{

    /*public function init()
    {
        $this->attributes = $this->buildDto->attributes;
    }*/

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
        $fileGenerator = $this->getFileGenerator();
        $classGenerator = $this->getClassGenerator();
        $classGenerator->setName($className);

        $fileGenerator->setUse('Symfony\Component\Validator\Constraints', 'Assert');
        $fileGenerator->setUse('Symfony\Component\Validator\Mapping\ClassMetadata');
        $this->addInterface('ZnCore\Domain\Interfaces\Entity\ValidateEntityByMetadataInterface');
        $this->addInterface('ZnCore\Domain\Interfaces\Entity\UniqueInterface');

        if(in_array('id', $this->attributes)) {
            $this->addInterface('ZnCore\Domain\Interfaces\Entity\EntityIdInterface');
        }

        if(in_array('created_at', $this->attributes)) {
            $fileGenerator->setUse(\DateTime::class);
            $constructBody = '$this->createdAt = new DateTime();';
            $classGenerator->addMethod('__construct', [], [], $constructBody);
        }

        $this->generateValidationRules($this->attributes);

        $methodGenerator = $this->generateUniqueMethod();
        $classGenerator->addMethodFromGenerator($methodGenerator);

        $this->generateAttributes($this->attributes);

        $fileGenerator->setNamespace($this->classNamespace());
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


    protected function generateValidationRulesBody(array $attributes): string {
        $validationRules = [];
        if ($attributes) {
            $constraintCodeGenerator = new ConstraintCodeGenerator($this->getFileGenerator());
            foreach ($attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);
                if($attribute !== 'id') {
                    $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\NotBlank());";
                }
                $validationRules = ArrayHelper::merge($validationRules, $constraintCodeGenerator->generateCode($attribute));
            }
        }
        $validateBody = implode(PHP_EOL, $validationRules);
        return $validateBody;
    }
}
