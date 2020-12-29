<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use ZnCore\Base\Legacy\Code\entities\ClassEntity;
use ZnCore\Base\Legacy\Code\entities\ClassUseEntity;
use ZnCore\Base\Legacy\Code\entities\ClassVariableEntity;
use ZnCore\Base\Legacy\Code\entities\InterfaceEntity;
use ZnCore\Base\Legacy\Code\enums\AccessEnum;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Helpers\ClassHelper;

abstract class BaseScenario
{

    public $domainNamespace;
    public $name;
    public $attributes;

    /** @var BuildDto */
    public $buildDto;

    abstract public function typeName();

    abstract public function classDir();

    public function init()
    {

    }

    public function run()
    {
        if ($this->isMakeInterface()) {
            $this->createInterface();
        }
        $this->createClass();
    }

    protected function isMakeInterface(): bool
    {
        return false;
    }

    protected function getClassName(): string
    {
        return Inflector::classify($this->buildDto->name) . $this->typeName();
    }

    protected function getFullClassName(): string
    {
        return $this->domainNamespace . '\\' . $this->classDir() . '\\' . $this->getClassName();
    }

    protected function interfaceDir()
    {
        return 'Interfaces\\' . $this->classDir();
    }

    protected function getInterfaceFullName(): string
    {
        return $this->domainNamespace . '\\' . $this->interfaceDir() . '\\' . $this->getInterfaceName();
    }

    protected function getInterfaceName(): string
    {
        $className = $this->getClassName();
        return $className . 'Interface';
    }

    protected function createInterface()
    {
        $fileGenerator = new FileGenerator;
        $interfaceGenerator = new InterfaceGenerator;
        $interfaceGenerator->setName($this->getInterfaceName());

        $fileGenerator->setClass($interfaceGenerator);
        $fileGenerator->setUse($this->getInterfaceFullName());
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        ClassHelper::generateFile($this->getInterfaceName(), $fileGenerator->generate());
    }

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $classGenerator->setName($className);
        if ($this->isMakeInterface()) {
            $classGenerator->setImplementedInterfaces([$this->getInterfaceName()]);
            $fileGenerator->setUse($this->getInterfaceFullName());
        }
        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $classGenerator->addProperties([
                    [Inflector::variablize($attribute)]
                ]);
            }
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());
    }
}
