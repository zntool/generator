<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;

class StringType extends BaseType
{

    public function getType(): string {
        return 'string';
    }

    public function isMatch(string $attributeName): bool
    {
        return
            $this->matchSuffixOrEqual($attributeName, 'title') ||
            $this->matchSuffixOrEqual($attributeName, 'name');
    }
}
