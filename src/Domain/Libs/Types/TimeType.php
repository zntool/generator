<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;

class TimeType extends BaseType
{

    public function getType(): string {
        return 'time';
    }

    public function isMatch(string $attributeName): bool
    {
        return TypeAttributeHelper::isMatchSuffix($attributeName, '_at');
    }
}
