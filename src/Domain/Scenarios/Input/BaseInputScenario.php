<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use ZnTool\Generator\Domain\Dto\BuildDto;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class BaseInputScenario
{

    public $helper;

    /** @var InputInterface */
    public $input;

    /** @var OutputInterface */
    public $output;

    /** @var BuildDto */
    public $buildDto;

    abstract protected function paramName();

    abstract protected function question(): Question;

    public function run()
    {
        $question = $this->question();
        $paramName = $this->paramName();
        $this->buildDto->{$paramName} = $this->ask($question);
    }

    protected function ask(Question $question)
    {
        return $this->helper->ask($this->input, $this->output, $question);
    }

}
