<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class DescriptionRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'description';
    }

    public function run(): string
    {
        return $this->renderCode('text', $this->attributeName, 'Описание');
    }

}
