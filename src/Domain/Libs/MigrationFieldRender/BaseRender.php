<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

abstract class BaseRender
{

    public $attributeName;

    abstract public function isMatch(): bool;

    abstract public function run(): string;

    protected function renderCode(string $type, string $attributeName, string $comment = '', string $extra = null): string
    {
        $code = "\$table->{$type}('{$attributeName}')";
        if ($extra) {
            $code .= $extra;
        }
        $code .= "->comment('{$comment}')";
        return $code . ';';
    }

}
