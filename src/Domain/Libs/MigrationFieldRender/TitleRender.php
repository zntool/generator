<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class TitleRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'title';
    }

    public function run(): string
    {
        return $this->renderCode('string', $this->attributeName, 'Название');
    }

}
