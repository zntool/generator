<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class OwnerIdRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'owner_id';
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName, 'ID учетной записи владельца');
    }

}
