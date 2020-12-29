<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class TypeModuleInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'typeModule';
    }

    protected function question(): Question
    {
        $typeArray = [
            'web',
            'api',
            'console',
        ];
        $question = new ChoiceQuestion(
            'Select types',
            $typeArray
        );
        //$question->setMultiselect(true);
        return $question;
    }

}
