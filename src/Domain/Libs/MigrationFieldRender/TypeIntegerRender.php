<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

use ZnTool\Generator\Domain\Libs\Types\IntType;

class TypeIntegerRender extends BaseRender
{

    public function isMatch(): bool
    {
        return IntType::match($this->attributeName);
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName);
    }

}
