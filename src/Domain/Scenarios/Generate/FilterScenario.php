<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Base\Libs\Store\StoreFile;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Generator\Domain\Libs\ConstraintCodeGenerator;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class FilterScenario extends BaseEntityScenario
{

    public function init()
    {
        $this->attributes = array_filter($this->getEntityAttributes(), function ($value) {
            return ! in_array($value, ['id', 'created_at', 'updated_at']);
        });
        //$this->attributes = $this->getEntityAttributes();
    }

    public function typeName()
    {
        return 'Filer';
    }

    public function classDir()
    {
        return 'Filters';
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

        $this->generateValidationRules($this->attributes);

        $this->generateAttributes($this->attributes);

        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);
        $fileGenerator->setSourceDirty(false);

        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $phpCode);
    }

    protected function generateValidationRulesBody(array $attributes): string
    {
        $validationRules = [];
        if ($attributes) {
            $constraintCodeGenerator = new ConstraintCodeGenerator($this->getFileGenerator());
            foreach ($attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);
                $validationRules = ArrayHelper::merge($validationRules, $constraintCodeGenerator->generateCode($attribute));
            }
        }
        $validateBody = implode(PHP_EOL, $validationRules);
        return $validateBody;
    }
}
