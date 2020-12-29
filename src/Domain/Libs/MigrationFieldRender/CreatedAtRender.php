<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class CreatedAtRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'created_at';
    }

    public function run(): string
    {
        return $this->renderCode('dateTime', $this->attributeName, 'Время создания');
    }

}
