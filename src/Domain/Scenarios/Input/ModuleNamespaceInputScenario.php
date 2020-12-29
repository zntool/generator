<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\Question;

class ModuleNamespaceInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'moduleNamespace';
    }

    protected function question(): Question
    {
        $question = new Question('Enter module namespace: ', 'App\\Web');
        return $question;
    }

}
