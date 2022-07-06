<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnCore\Domain\Interfaces\DomainInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;

class DomainScenario extends BaseScenario
{

    public function typeName()
    {
        return 'Domain';
    }

    public function classDir()
    {
        return '';
    }

    public function classNamespace(): string
    {
        return ;
    }

    protected function createClass()
    {
        $fileGenerator = $this->getFileGenerator();
        $classGenerator = $this->getClassGenerator();
        $classGenerator->setName('Domain');
        $this->addInterface(DomainInterface::class);
//        $classGenerator->setImplementedInterfaces(['DomainInterface']);
        $classGenerator->addMethods([
            MethodGenerator::fromArray([
                'name' => 'getName',
                'body' => "return '{$this->buildDto->domainName}';",
            ]),
        ]);
//        $fileGenerator->setClass($classGenerator);
//        $fileGenerator->setUse(DomainInterface::class);
        $fileGenerator->setNamespace($this->classNamespace());
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . 'Domain', $fileGenerator->generate());
    }
}
