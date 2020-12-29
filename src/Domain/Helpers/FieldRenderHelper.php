<?php

namespace ZnTool\Generator\Domain\Helpers;

class FieldRenderHelper
{

    public static function isMatchPrefix(string $attributeName, string $prefixName): bool
    {
        return strpos($attributeName, $prefixName) === 0;
    }

    public static function isMatchSuffix(string $attributeName, string $suffixName): bool
    {
        return strpos($attributeName, $suffixName) == strlen($attributeName) - strlen($suffixName);
    }

    public static function renderCode(string $type, string $attributeName, string $comment = '', string $extra = null): string
    {
        $code = "\$table->{$type}('{$attributeName}')";
        if ($extra) {
            $code .= $extra;
        }
        $code .= "->comment('{$comment}')";
        return $code . ';';
    }

}
