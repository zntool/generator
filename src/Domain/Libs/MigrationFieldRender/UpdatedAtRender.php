<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class UpdatedAtRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'updated_at';
    }

    public function run(): string
    {
        return $this->renderCode('dateTime', $this->attributeName, 'Время обновления', '->nullable()');
    }

}
