<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

use ZnTool\Generator\Domain\Libs\Types\ArrayType;

class ArrayRender extends BaseRender
{

    public function isMatch(): bool
    {
        return ArrayType::match($this->attributeName);
    }

    public function run(): string
    {
        return $this->renderCode('text', $this->attributeName, 'Данные');
    }

}
