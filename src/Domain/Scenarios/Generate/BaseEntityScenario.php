<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Base\Instance\Helpers\InstanceHelper;
use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Text\Helpers\Inflector;
use ZnCore\Validation\Interfaces\ValidationByMetadataInterface;

abstract class BaseEntityScenario extends BaseScenario
{

    //abstract protected function generateValidationRulesBody(array $attributes): string;

    public function getEntityAttributes(): array
    {
        $entityScenario = $this->createGenerator(EntityScenario::class);
        $entityClass = $entityScenario->getFullClassName();

        $attributes = [];

        if (class_exists($entityClass)) {
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
        if ($this->buildDto->attributes) {
            $this->attributes = $this->buildDto->attributes;
        } else {
            $this->attributes = $this->getEntityAttributes();
        }
    }

    protected function generateValidationRules(array $attributes)
    {
        $this->addInterface(ValidationByMetadataInterface::class);
        $this->getFileGenerator()->setUse('Symfony\Component\Validator\Constraints', 'Assert');
        $this->getFileGenerator()->setUse(ClassMetadata::class);
        $validateBody = $this->generateValidationRulesBody($attributes);
        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('metadata');
        $parameterGenerator->setType(ClassMetadata::class);
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

    protected function generateValidationRulesBody(array $attributes): string
    {
        $validationRules = [];
        if ($attributes) {
//            $constraintCodeGenerator = new ConstraintCodeGenerator($this->getFileGenerator());
            foreach ($attributes as $attribute) {
                $validationRules = ArrayHelper::merge($validationRules, $this->generateValidationRulesForAttribute($attribute));
            }
        }
        $validateBody = implode(PHP_EOL, $validationRules);
        return $validateBody;
    }
}
