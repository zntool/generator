<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class StatusRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'status';
    }

    public function run(): string
    {
        return $this->renderCode('smallInteger', $this->attributeName, 'Статус');
    }

}
