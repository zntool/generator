<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use ZnLib\Console\Symfony4\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class TypeInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'types';
    }

    protected function question(): Question
    {
        $question = new ChoiceQuestion(
            'Select types',
            $this->buildDto->typeArray,
            'a'
        );
        $question->setMultiselect(true);
        return $question;
    }

    public function run()
    {
        $question = $this->question();
        $this->buildDto->types = $this->ask($question);
    }

}
