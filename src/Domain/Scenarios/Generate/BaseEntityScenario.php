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

abstract class BaseEntityScenario extends BaseScenario
{

    abstract protected function generateValidationRulesBody(array $attributes): string;

    public function getEntityAttributes(): array {
        $entityScenario = new EntityScenario();
        $entityScenario->name = $this->name;
        $entityScenario->buildDto = $this->buildDto;
        $entityScenario->domainNamespace = $this->domainNamespace;
        $entityClass = $entityScenario->getFullClassName();

        $attributes = [];

        if(class_exists($entityClass)) {
            $reflectionClass = new \ReflectionClass($entityClass);
            $entityAttributes = $reflectionClass->getProperties();
            foreach ($entityAttributes as $entityAttribute) {
                $attributeName = $entityAttribute->getName();
                $attributes[] = Inflector::underscore($attributeName);
            }
        }

        return $attributes;
    }

    public function init()
    {
        $this->attributes = $this->buildDto->attributes;
    }

    protected function generateValidationRules(array $attributes)
    {
        $validateBody = $this->generateValidationRulesBody($attributes);
        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('metadata');
        $parameterGenerator->setType('Symfony\Component\Validator\Mapping\ClassMetadata');
        $this->getClassGenerator()->addMethod('loadValidatorMetadata', [$parameterGenerator], [MethodGenerator::FLAG_STATIC], $validateBody);
    }

    protected function generateAttributes(array $attributes)
    {
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);

                $propertyGenerator = new PropertyGenerator($attributeName, null, PropertyGenerator::FLAG_PROTECTED);
//                $propertyGenerator->setDefaultValue();
                $this->getClassGenerator()->addPropertyFromGenerator($propertyGenerator);

                $setterMethodGenerator = $this->generateSetter($attributeName);
                $this->getClassGenerator()->addMethodFromGenerator($setterMethodGenerator);

                $getterMethodGenerator = $this->generateGetter($attributeName);
                $this->getClassGenerator()->addMethodFromGenerator($getterMethodGenerator);
            }
        }
    }

    protected function generateSetter(string $attributeName): MethodGenerator
    {
        $methodBody = '$this->' . $attributeName . ' = $value;';
        $methodName = 'set' . Inflector::camelize($attributeName);
        $methodGenerator = new MethodGenerator($methodName, ['value']);
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('void');
        return $methodGenerator;
    }

    protected function generateGetter(string $attributeName): MethodGenerator
    {
        $methodBody = 'return $this->' . $attributeName . ';';
        $methodName = 'get' . Inflector::camelize($attributeName);
        $methodGenerator = new MethodGenerator($methodName);
        $methodGenerator->setBody($methodBody);
        //$methodGenerator->setReturnType('void');
        return $methodGenerator;
    }
}
