<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;

class IntType extends BaseType
{

    public function getType(): string {
        return 'int';
    }

    public function isMatch(string $attributeName): bool
    {
        return
            TypeAttributeHelper::isMatchTypeByClass($attributeName, IntPositiveType::class) ||
            TypeAttributeHelper::isMatchTypeByClass($attributeName, IntPositiveOrZeroType::class) ||
            TypeAttributeHelper::isMatchTypeByClass($attributeName, StatusIdType::class);
    }
}
