<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class UserIdRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'identity_id' || $this->attributeName == 'user_id';
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName, 'ID учетной записи пользователя');
    }

}
