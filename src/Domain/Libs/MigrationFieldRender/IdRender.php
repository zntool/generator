<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class IdRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'id';
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName, 'Идентификатор', '->autoIncrement()');
    }

}
