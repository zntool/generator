<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;
use ZnTool\Generator\Domain\Libs\Types\TimeType;

class TypeTimeRender extends BaseRender
{

    public function isMatch(): bool
    {
        return TypeAttributeHelper::isMatchTypeByClass($this->attributeName, TimeType::class);
    }

    public function run(): string
    {
        return $this->renderCode('dateTime', $this->attributeName);
    }
}
