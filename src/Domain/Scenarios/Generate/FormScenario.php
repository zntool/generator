<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Text\Helpers\Inflector;
use ZnTool\Generator\Domain\Libs\ConstraintCodeGenerator;

class FormScenario extends FilterScenario
{

    public function typeName()
    {
        return 'Form';
    }

    public function classDir()
    {
        return 'Forms';
    }

    protected function generateValidationRulesForAttribute(string $attribute, ConstraintCodeGenerator $constraintCodeGenerator = null): array
    {
        $attributeName = Inflector::variablize($attribute);
        $validationRules = [];
        $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\NotBlank());";
        $constraintCodeGenerator = new ConstraintCodeGenerator($this->getFileGenerator());
        $validationRules = ArrayHelper::merge($validationRules, $constraintCodeGenerator->generateCode($attribute));
        return $validationRules;
    }
}
