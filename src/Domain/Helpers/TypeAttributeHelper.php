<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\Instance\Helpers\ClassHelper;
use ZnCore\Text\Helpers\Inflector;
use ZnTool\Generator\Domain\Libs\Types\BaseType;

class TypeAttributeHelper
{

    public static function isMatchPrefix(string $attributeName, string $prefixName): bool
    {
//        return strpos($attributeName, $prefixName) === 0;
        return preg_match("/^$prefixName/i", $attributeName);
    }

    public static function isMatchSuffix(string $attributeName, string $suffixName): bool
    {
        return preg_match("/$suffixName$/i", $attributeName);
    }

    public static function isMatchTypeByClass(string $attributeName, $typeClass): bool {
        /** @var BaseType $typeInstance */
        $typeInstance = ClassHelper::createInstance($typeClass);
        return $typeInstance->isMatch(Inflector::underscore($attributeName));
    }
}
