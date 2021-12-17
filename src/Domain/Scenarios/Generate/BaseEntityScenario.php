<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;

abstract class BaseEntityScenario extends BaseScenario
{

    //abstract protected function generateValidationRulesBody(array $attributes): string;

    public function getEntityAttributes(): array
    {
        $entityScenario = new EntityScenario();
        $entityScenario->name = $this->name;
        $entityScenario->buildDto = $this->buildDto;
        $entityScenario->domainNamespace = $this->domainNamespace;
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
