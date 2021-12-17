<?php

namespace ZnTool\Generator\Domain\Libs\Types;

class ArrayType extends BaseType
{

    public function getType(): string
    {
        return 'array';
    }

    public function isMatch(string $attributeName): bool
    {
        return
            $this->matchSuffixOrEqual($attributeName, 'data') ||
            $this->matchSuffixOrEqual($attributeName, 'attributes') ||
            $this->matchSuffixOrEqual($attributeName, 'props');
    }
}
