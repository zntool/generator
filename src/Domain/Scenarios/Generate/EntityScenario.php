<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Text\Helpers\Inflector;
use ZnLib\Components\Store\StoreFile;
use ZnCore\Entity\Interfaces\EntityIdInterface;
use ZnCore\Entity\Interfaces\UniqueInterface;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Libs\ConstraintCodeGenerator;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class EntityScenario extends BaseEntityScenario
{

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

        if (in_array('id', $this->attributes)) {
            $this->addInterface(EntityIdInterface::class);
        }

        if (in_array('created_at', $this->attributes)) {
            $fileGenerator->setUse(\DateTime::class);
            $constructBody = '$this->createdAt = new DateTime();';
            $classGenerator->addMethod('__construct', [], [], $constructBody);
        }

        $this->generateValidationRules($this->attributes);
        $this->generateUniqueMethod();
        $this->generateAttributes($this->attributes);

        $fileGenerator->setNamespace($this->classNamespace());
//        $fileGenerator->setClass($classGenerator);
        $fileGenerator->setSourceDirty(false);

        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($this->getFullClassName(), $phpCode);

        $this->updateContainerConfig($fileGenerator);
    }

    private function updateContainerConfig(FileGenerator $fileGenerator)
    {
        $fullClassName = $this->getFullClassName();
        $containerFileName = PackageHelper::pathByNamespace($this->domainNamespace) . '/config/container.php';
        $storeFile = new StoreFile($containerFileName);
        $containerConfig = $storeFile->load();

        $repoGen = $this->createGenerator(RepositoryScenario::class);
        $containerConfig['entities'][$fullClassName] = $repoGen->getInterfaceFullName();
        $storeFile->save($containerConfig);
    }

    private function generateUniqueMethod()
    {
        $this->addInterface(UniqueInterface::class);
        $methodBody = "return [];";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('unique');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('array');
        $this->getClassGenerator()->addMethodFromGenerator($methodGenerator);
    }

    protected function generateValidationRulesForAttribute(string $attribute, ConstraintCodeGenerator $constraintCodeGenerator = null): array {
        $attributeName = Inflector::variablize($attribute);
        $validationRules = [];
        if ($attribute !== 'id') {
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\NotBlank());";
        }
        $constraintCodeGenerator = new ConstraintCodeGenerator($this->getFileGenerator());
        $validationRules = ArrayHelper::merge($validationRules, $constraintCodeGenerator->generateCode($attribute));
        return $validationRules;
    }
}
