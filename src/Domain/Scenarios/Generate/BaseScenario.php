<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Helpers\ClassHelper;

abstract class BaseScenario
{

    public $domainNamespace;
    public $name;
    public $attributes;

    private $fileGenerator;
    private $classGenerator;

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

    public function getFileGenerator(): FileGenerator
    {
        if ($this->fileGenerator == null) {
            $this->fileGenerator = new FileGenerator();
        }
        return $this->fileGenerator;
    }

    public function getClassGenerator(): ClassGenerator
    {
        if ($this->classGenerator == null) {
            $this->classGenerator = new ClassGenerator();
        }
        return $this->classGenerator;
    }

    protected function getClassName(): string
    {
        return Inflector::classify($this->buildDto->name) . $this->typeName();
    }

    public function getFullClassName(): string
    {
        return $this->classNamespace() . '\\' . $this->getClassName();
    }

    protected function bundleNamespace(): string
    {
        return \ZnCore\Base\Helpers\ClassHelper::getNamespace($this->domainNamespace);
    }

    public function classNamespace(): string
    {
        return $this->domainNamespace . '\\' . $this->classDir();
    }

    protected function interfaceDir()
    {
        return 'Interfaces\\' . $this->classDir();
    }

    public function getInterfaceFullName(): string
    {
        //return $this->classNamespace() . '\\' . $this->getInterfaceName();
        return $this->domainNamespace . '\\' . $this->interfaceDir() . '\\' . $this->getInterfaceName();
    }

    public function getInterfaceName(): string
    {
        $className = $this->getClassName();
        return $className . 'Interface';
    }

    protected $implementedInterfaces = [];

    protected function addInterface(string $interface, ?string $as = null)
    {
        $this->getFileGenerator()->setUse($interface, $as);
        $this->implementedInterfaces[] = basename($interface);
        $this->implementedInterfaces = array_unique($this->implementedInterfaces);
        $this->getClassGenerator()->setImplementedInterfaces($this->implementedInterfaces);
    }

    protected function createInterface()
    {
        $fileGenerator = new FileGenerator();
        $interfaceGenerator = new InterfaceGenerator;
        $interfaceGenerator->setName($this->getInterfaceName());

        $fileGenerator->setClass($interfaceGenerator);
//        $fileGenerator->setUse($this->getInterfaceFullName());
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        ClassHelper::generateFile($this->getInterfaceFullName(), $fileGenerator->generate());
    }

    protected function generateFileCode(FileGenerator $fileGenerator)
    {
        $phpCode = $fileGenerator->generate();
        foreach ($fileGenerator->getUses() as $useItem) {
            $useClass = $useItem[0];
            $phpCode = str_replace('\\' . $useClass, \ZnCore\Base\Helpers\ClassHelper::getClassOfClassName($useClass), $phpCode);
        }
        return $phpCode;
    }

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = $this->getFileGenerator();
        $classGenerator = $this->getClassGenerator();
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
        $fileGenerator->setNamespace($this->classNamespace());
        $fileGenerator->setClass($classGenerator);
        ClassHelper::generateFile($this->getFullClassName(), $fileGenerator->generate());
    }
}
