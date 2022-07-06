<?php

namespace ZnTool\Generator\Domain\Services;

use ZnCore\Text\Helpers\Inflector;
use ZnCore\Instance\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Interfaces\Services\ModuleServiceInterface;
use ZnTool\Generator\Domain\Scenarios\Generate\BaseScenario;

class ModuleService implements ModuleServiceInterface
{

    public function generate(BuildDto $buildDto)
    {
        $type = Inflector::classify($buildDto->typeModule);
        $scenarioInstance = $this->createScenarioByTypeName($type);
        $scenarioParams = [
            'buildDto' => $buildDto,
            'moduleNamespace' => $buildDto->moduleNamespace,
        ];
        ClassHelper::configure($scenarioInstance, $scenarioParams);
        $scenarioInstance->init();
        $scenarioInstance->run();
    }

    private function createScenarioByTypeName($type): BaseScenario
    {
        $scenarioClass = 'ZnTool\\Generator\\Domain\Scenarios\\Generate\\' . $type . 'Scenario';
        /** @var BaseScenario $scenarioInstance */
        $scenarioInstance = new $scenarioClass;
        return $scenarioInstance;
    }

}
