<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

class FormScenario extends FilterScenario
{

    public function typeName()
    {
        return 'Form';
    }

    public function classDir()
    {
        return 'Forms';
    }
}
