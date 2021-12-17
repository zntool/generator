<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;

abstract class BaseType
{

    abstract public function getType(): string;
    abstract public function isMatch(string $attributeName): bool;

    protected function matchSuffixOrEqual(string $attributeName, string $match): bool {
        return TypeAttributeHelper::isMatchSuffix($attributeName, '_' . $match) || $attributeName == $match;
    }
}
