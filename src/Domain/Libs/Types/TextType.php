<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;

class TextType extends StringType
{

    public function isMatch(string $attributeName): bool
    {
        return
            $this->matchSuffixOrEqual($attributeName, 'text') ||
            $this->matchSuffixOrEqual($attributeName, 'description') ||
            $this->matchSuffixOrEqual($attributeName, 'content');
    }
}
