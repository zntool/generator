<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;

class StatusIdType extends IntType
{

    public function isMatch(string $attributeName): bool
    {
        return $attributeName == 'status_id';
    }
}
