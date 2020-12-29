<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class SizeRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'size';
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName, 'Размер');
    }

}
