<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;

abstract class BaseType
{

    abstract public function getType(): string;
    abstract public function isMatch(string $attributeName): bool;
}
