<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use ZnCore\Base\Legacy\Code\entities\ClassEntity;
use ZnCore\Base\Legacy\Code\entities\ClassUseEntity;
use ZnCore\Base\Legacy\Code\entities\ClassVariableEntity;
use ZnCore\Base\Legacy\Code\entities\InterfaceEntity;
use ZnCore\Base\Legacy\Code\enums\AccessEnum;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnTool\Generator\Domain\Dto\BuildDto;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\PropertyValueGenerator;

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
        $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface');
        $implementedInterfaces[] = 'ValidateEntityInterface';

        if(in_array('id', $this->attributes)) {
            $fileGenerator->setUse('ZnCore\Domain\Interfaces\Entity\EntityIdInterface');
            $implementedInterfaces[] = 'EntityIdInterface';
        }

        $classGenerator->setImplementedInterfaces($implementedInterfaces);

        $validateBody = $this->generateValidationRulesBody($this->attributes);
        $classGenerator->addMethod('validationRules', [], [], $validateBody);

        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);

                $propertyGenerator = new PropertyGenerator($attributeName, null, PropertyGenerator::FLAG_PRIVATE);
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
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());
    }

    private function generateValidationRulesBody(array $attributes): string {
        $validationRules = [];
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);
                $validationRules[] =
                    "    '$attributeName' => [
        new Assert\NotBlank,
    ],";
            }
        }
        $validateBody = 'return [' . PHP_EOL . implode(PHP_EOL, $validationRules) . PHP_EOL . '];';
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
