<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Text\Helpers\Inflector;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Libs\ConstraintCodeGenerator;

class FilterScenario extends BaseEntityScenario
{

    public function typeName()
    {
        return 'Filter';
    }

    public function classDir()
    {
        return 'Filters';
    }

    public function init()
    {
        parent::init();
        $this->attributes = array_filter($this->attributes, function ($value) {
            return !in_array($value, ['id', 'created_at', 'updated_at']);
        });
    }

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = $this->getFileGenerator();
        $classGenerator = $this->getClassGenerator();
        $classGenerator->setName($className);

        $this->generateValidationRules($this->attributes);

        $this->generateAttributes($this->attributes);

        $fileGenerator->setNamespace($this->classNamespace());
//        $fileGenerator->setClass($classGenerator);
        $fileGenerator->setSourceDirty(false);

        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($this->getFullClassName(), $phpCode);
    }

    protected function generateValidationRulesForAttribute(string $attribute, ConstraintCodeGenerator $constraintCodeGenerator = null): array {
        $attributeName = Inflector::variablize($attribute);
        $validationRules = [];
        /*if ($attribute !== 'id') {
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\NotBlank());";
        }*/
        $constraintCodeGenerator = new ConstraintCodeGenerator($this->getFileGenerator());
        $validationRules = ArrayHelper::merge($validationRules, $constraintCodeGenerator->generateCode($attribute));
        return $validationRules;
    }

    /*protected function generateValidationRulesBody(array $attributes): string
    {
        $validationRules = [];
        if ($attributes) {
            $constraintCodeGenerator = new ConstraintCodeGenerator($this->getFileGenerator());
            foreach ($attributes as $attribute) {
//                $attributeName = Inflector::variablize($attribute);
                $validationRules = ArrayHelper::merge($validationRules, $constraintCodeGenerator->generateCode($attribute));
            }
        }
        $validateBody = implode(PHP_EOL, $validationRules);
        return $validateBody;
    }*/
}
