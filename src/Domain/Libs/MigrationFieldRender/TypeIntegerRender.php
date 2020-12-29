<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;

class TypeIntegerRender extends BaseRender
{

    public function isMatch(): bool
    {
        return FieldRenderHelper::isMatchSuffix($this->attributeName, '_id');
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName);
    }

}
