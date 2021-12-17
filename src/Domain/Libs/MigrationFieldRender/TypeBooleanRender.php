<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;
use ZnTool\Generator\Domain\Libs\Types\BoolType;

class TypeBooleanRender extends BaseRender
{

    public function isMatch(): bool
    {
        return TypeAttributeHelper::isMatchTypeByClass($this->attributeName, BoolType::class);
    }

    public function run(): string
    {
        return $this->renderCode('boolean', $this->attributeName);
    }

}
