<?php

namespace ZnTool\Generator\Domain\Libs\Types;

class IntType extends BaseType
{

    public function getType(): string
    {
        return 'int';
    }

    public function isMatch(string $attributeName): bool
    {
        return
            IntPositiveType::match($attributeName) ||
            IntPositiveOrZeroType::match($attributeName) ||
            StatusIdType::match($attributeName);
    }
}
