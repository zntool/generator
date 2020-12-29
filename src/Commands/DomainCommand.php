<?php

namespace ZnTool\Generator\Commands;

use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Interfaces\Services\DomainServiceInterface;
use ZnTool\Generator\Domain\Scenarios\Input\DomainNameInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\DomainNamespaceInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\DriverInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\EntityAttributesInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\IsCrudRepositoryInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\IsCrudServiceInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\NameInputScenario;
use ZnTool\Generator\Domain\Scenarios\Input\TypeInputScenario;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DomainCommand extends BaseGeneratorCommand
{

    protected static $defaultName = 'generator:domain';
    private $domainService;

    public function __construct(?string $name = null, DomainServiceInterface $domainService)
    {
        parent::__construct($name);
        $this->domainService = $domainService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<fg=white># Domain generator</>');
        $buildDto = new BuildDto;
        $buildDto->typeArray = ['service', 'repository', 'entity', 'migration', 'domain'];
        $this->input($input, $output, $buildDto);
        $this->domainService->generate($buildDto);
        return 0;
    }

    private function input(InputInterface $input, OutputInterface $output, BuildDto $buildDto)
    {
        /*$buildDto->domainNamespace = 'App\\Domain';
        $buildDto->domainName = 'app';
        $buildDto->types = array_keys($buildDto->typeArray);
        $buildDto->name = 'qwerty';
        $buildDto->attributes = ['id', 'category_id', 'title', 'author', 'is_archive', 'status', 'size', 'created_at'];
        $buildDto->isCrudService = true;
        $buildDto->isCrudRepository = true;
        $buildDto->driver = [
            'eloquent',
            'file',
        ];
        return;*/


        $this->runInputScenario(DomainNamespaceInputScenario::class, $input, $output, $buildDto);

        $domainClass = $buildDto->domainNamespace . '\\Domain';
        if (class_exists($domainClass)) {
            $domainInstance = new $domainClass;
            $buildDto->domainName = $domainInstance->getName();
            $output->writeln('');
            $output->writeln("<fg=green>Domain founded ({$buildDto->domainName})</>");
        }
        if (empty($buildDto->domainName)) {
            $this->runInputScenario(DomainNameInputScenario::class, $input, $output, $buildDto);
        }

        $this->runInputScenario(TypeInputScenario::class, $input, $output, $buildDto);
        $this->runInputScenario(NameInputScenario::class, $input, $output, $buildDto);

        if (in_array('entity', $buildDto->types)) {
            $this->runInputScenario(EntityAttributesInputScenario::class, $input, $output, $buildDto);
        }

        if (in_array('service', $buildDto->types)) {
            $this->runInputScenario(IsCrudServiceInputScenario::class, $input, $output, $buildDto);
        }

        if (in_array('repository', $buildDto->types)) {
            $this->runInputScenario(DriverInputScenario::class, $input, $output, $buildDto);
            $this->runInputScenario(IsCrudRepositoryInputScenario::class, $input, $output, $buildDto);
        }
    }

}
