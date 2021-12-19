<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;

class I18nType extends ArrayType
{

    public function isMatch(string $attributeName): bool
    {
        return TypeAttributeHelper::isMatchSuffix($attributeName, '_i18n');
    }
}
