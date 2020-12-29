<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\Question;

class EntityAttributesInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'attributes';
    }

    protected function question(): Question
    {
        $question = new Question('Enter entity attribute: ');
        return $question;
    }

    public function run()
    {
        $buildDto = $this->buildDto;

        $question = $this->question();
        do {
            $attribute = $this->ask($question);
            $attribute = trim($attribute);
            if ($attribute) {
                $this->buildDto->attributes[] = $attribute;
            }
        } while ( ! empty($attribute));
    }

}
