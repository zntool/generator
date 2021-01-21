<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class IsCrudControllerInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'isCrudController';
    }

    protected function question(): Question
    {
        $question = new ConfirmationQuestion(
            'Is CRUD controller? (y|N): ',
            false
        );
        return $question;
    }

}
