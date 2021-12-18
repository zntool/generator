<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

use ZnTool\Generator\Domain\Libs\Types\TimeType;

class TypeTimeRender extends BaseRender
{

    public function isMatch(): bool
    {
        return TimeType::match($this->attributeName);
    }

    public function run(): string
    {
        return $this->renderCode('dateTime', $this->attributeName);
    }
}
