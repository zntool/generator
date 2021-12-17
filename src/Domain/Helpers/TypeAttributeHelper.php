<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnTool\Generator\Domain\Libs\Types\BaseType;

class TypeAttributeHelper
{

    public static function isMatchPrefix(string $attributeName, string $prefixName): bool
    {
        return strpos($attributeName, $prefixName) === 0;
    }

    public static function isMatchSuffix(string $attributeName, string $suffixName): bool
    {
        return strpos($attributeName, $suffixName) == strlen($attributeName) - strlen($suffixName);
    }

    public static function isMatchTypeByClass(string $attributeName, $typeClass): bool {
        /** @var BaseType $typeInstance */
        $typeInstance = ClassHelper::createInstance($typeClass);
        return $typeInstance->isMatch(Inflector::underscore($attributeName));
    }
}
