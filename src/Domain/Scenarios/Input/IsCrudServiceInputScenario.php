<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class IsCrudServiceInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'isCrudService';
    }

    protected function question(): Question
    {
        $question = new ConfirmationQuestion(
            'Is CRUD service? (Y|n): ',
            true
        );
        return $question;
    }

}
