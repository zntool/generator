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

        $implementedInterfaces = [];
        $fileGenerator->setUse('Symfony\Component\Validator\Constraints', 'Assert');
//        $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface');
//        $implementedInterfaces[] = 'ValidateEntityInterface';

        $fileGenerator->setUse('Symfony\Component\Validator\Mapping\ClassMetadata');

        $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\ValidateEntityByMetadataInterface');
//        $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\UniqueInterface');
        $implementedInterfaces[] = 'ValidateEntityByMetadataInterface';
//        $implementedInterfaces[] = 'UniqueInterface';

        /*if (in_array('id', $this->attributes)) {
            $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\EntityIdInterface');
            $implementedInterfaces[] = 'EntityIdInterface';
        }*/

        $classGenerator->setImplementedInterfaces($implementedInterfaces);

        /*if (in_array('created_at', $this->attributes)) {
            $fileGenerator->setUse(\DateTime::class);
            $constructBody = '$this->createdAt = new DateTime();';
            $classGenerator->addMethod('__construct', [], [], $constructBody);
        }*/

        $validateBody = $this->generateValidationRulesBody($this->attributes, $fileGenerator);
        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('metadata');
        $parameterGenerator->setType('Symfony\Component\Validator\Mapping\ClassMetadata');
        $classGenerator->addMethod('loadValidatorMetadata', [$parameterGenerator], [MethodGenerator::FLAG_STATIC], $validateBody);

//        $methodGenerator = $this->generateUniqueMethod();
//        $classGenerator->addMethodFromGenerator($methodGenerator);
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
    }

    /*private function generateUniqueMethod(): MethodGenerator
    {
        $methodBody = "return [];";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('unique');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('array');
        return $methodGenerator;
    }*/


    private function generateValidationRulesBody(array $attributes, FileGenerator $fileGenerator): string
    {
        $validationRules = [];
        if ($attributes) {
            $constraintCodeGenerator = new ConstraintCodeGenerator($fileGenerator);
            foreach ($attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);
                //if ($attribute !== 'id') {
                    $validationRules = ArrayHelper::merge($validationRules, $constraintCodeGenerator->generateCode($attribute));
                //}
            }
        }
        $validateBody = implode(PHP_EOL, $validationRules);
        return $validateBody;
    }
}
