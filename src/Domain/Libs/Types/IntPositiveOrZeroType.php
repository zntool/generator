<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;

class IntPositiveOrZeroType extends IntType
{

    public function isMatch(string $attributeName): bool
    {
        return
            TypeAttributeHelper::isMatchSuffix($attributeName, '_count') ||
            $attributeName == 'count' ||
            TypeAttributeHelper::isMatchSuffix($attributeName, '_size') ||
            $attributeName == 'size';
    }
}
