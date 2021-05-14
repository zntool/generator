<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class AuthorIdRender extends BaseRender
{

    public function isMatch(): bool
    {
        return $this->attributeName == 'author_id';
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName, 'ID учетной записи автора');
    }

}
