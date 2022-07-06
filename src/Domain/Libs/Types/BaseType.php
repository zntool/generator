<?php

namespace ZnTool\Generator\Domain\Libs\Types;

use ZnCore\Instance\Helpers\ClassHelper;
use ZnCore\Text\Helpers\Inflector;
use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;

abstract class BaseType
{

    abstract public function getType(): string;

    abstract public function isMatch(string $attributeName): bool;

    public static function match(string $attributeName): bool
    {
        $typeInstance = self::getInstance();
        return $typeInstance->isMatch(Inflector::underscore($attributeName));
    }

    /**
     * @return BaseType
     */
    public static function getInstance()
    {
        /** @var BaseType $typeInstance */
        $typeInstance = ClassHelper::createInstance(static::class);
        return $typeInstance;
    }

    protected function matchSuffixOrEqual(string $attributeName, string $match): bool
    {
        return TypeAttributeHelper::isMatchSuffix($attributeName, '_' . $match) || $attributeName == $match;
    }
}
