<?php

namespace ZnTool\Generator\Domain\Interfaces\Services;

use ZnTool\Generator\Domain\Dto\BuildDto;

interface DomainServiceInterface
{

    public function generate(BuildDto $buildDto);

}