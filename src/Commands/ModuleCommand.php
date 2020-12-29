<?php

namespace ZnTool\Generator\Commands;

use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Interfaces\Services\ModuleServiceInterface;
use ZnTool\Generator\Domain\Scenarios\Input\ModuleNamespaceInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\NameInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\TypeModuleInputScenario;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleCommand extends BaseGeneratorCommand
{

    protected static $defaultName = 'generator:module';
    private $moduleService;

    public function __construct(?string $name = null, ModuleServiceInterface $moduleService)
    {
        parent::__construct($name);
        $this->moduleService = $moduleService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=white># Module generator</>');
        $buildDto = new BuildDto;
        $this->input($input, $output, $buildDto);
        $this->moduleService->generate($buildDto);
        return 0;
    }

    private function input(InputInterface $input, OutputInterface $output, BuildDto $buildDto)
    {
        /*$buildDto->moduleNamespace = 'App\\Api';
        $buildDto->typeModule = 'api';
        //$buildDto->moduleNamespace = 'App\\Web';
        //$buildDto->typeModule = 'web';

        $buildDto->moduleName = 'app';
        $buildDto->name = 'qwerty';
        $buildDto->endpoint = 'qwerty';

        return;*/

        $this->runInputScenario(ModuleNamespaceInputScenario::class, $input, $output, $buildDto);
        $this->runInputScenario(TypeModuleInputScenario::class, $input, $output, $buildDto);
        $this->runInputScenario(NameInputScenario::class, $input, $output, $buildDto);
    }

}
