<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\Question;

class DomainNamespaceInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'domainNamespace';
    }

    protected function question(): Question
    {
        $question = new Question('Enter domain namespace: ', 'App\\Domain');
        return $question;
    }

}
