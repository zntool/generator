<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\InterfaceGenerator;

abstract class BaseInterfaceScenario extends BaseScenario
{

    public function __construct()
    {
        $this->setClassGenerator(new InterfaceGenerator());
    }
}
