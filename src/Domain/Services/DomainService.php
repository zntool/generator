<?php

namespace ZnTool\Generator\Domain\Services;

use ZnCore\Text\Helpers\Inflector;
use ZnCore\Instance\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Interfaces\Services\DomainServiceInterface;
use ZnTool\Generator\Domain\Scenarios\Generate\BaseScenario;

class DomainService implements DomainServiceInterface
{

    public function generate(BuildDto $buildDto)
    {
        foreach ($buildDto->types as $typeName) {
            $type = $typeName;
            $type = Inflector::classify($type);
            $scenarioInstance = $this->createScenarioByTypeName($type);
            $scenarioParams = [
                'name' => $buildDto->name,
                'driver' => $buildDto->driver,
                'buildDto' => $buildDto,
                'domainNamespace' => $buildDto->domainNamespace,
            ];
            ClassHelper::configure($scenarioInstance, $scenarioParams);
            $scenarioInstance->init();
            $scenarioInstance->run();
        }
    }

    private function createScenarioByTypeName($type): BaseScenario
    {
        $scenarioClass = 'ZnTool\\Generator\\Domain\Scenarios\\Generate\\' . $type . 'Scenario';
        /** @var BaseScenario $scenarioInstance */
        $scenarioInstance = new $scenarioClass;
        return $scenarioInstance;
    }

}
