<?php

namespace ZnTool\Generator\Domain\Libs\MigrationFieldRender;

use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;
use ZnTool\Generator\Domain\Libs\Types\StatusIdType;

class StatusRender extends BaseRender
{

    public function isMatch(): bool
    {
        return TypeAttributeHelper::isMatchTypeByClass($this->attributeName, StatusIdType::class);
    }

    public function run(): string
    {
        return $this->renderCode('smallInteger', $this->attributeName, 'Статус');
    }

}
