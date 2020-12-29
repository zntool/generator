<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

class MiscRender extends BaseRender
{

    public function isMatch(): bool
    {
        return true;
    }

    public function run(): string
    {
        return $this->renderCode('string', $this->attributeName);
    }

}
