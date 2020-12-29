<?php

namespace ZnTool\Generator\Domain\Interfaces\Services;

use ZnTool\Generator\Domain\Dto\BuildDto;

interface ModuleServiceInterface
{

    public function generate(BuildDto $buildDto);

}
