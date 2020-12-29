<?php

namespace ZnTool\Generator\Commands;

use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Scenarios\Input\BaseInputScenario;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseGeneratorCommand extends Command
{

    protected function runInputScenario(string $className, InputInterface $input, OutputInterface $output, BuildDto $dto)
    {
        $output->writeln('');
        /** @var BaseInputScenario $inputScenario */
        $inputScenario = new $className;
        $inputScenario->helper = $this->getHelper('question');
        $inputScenario->input = $input;
        $inputScenario->output = $output;
        $inputScenario->buildDto = $dto;
        return $inputScenario->run();
    }

}
