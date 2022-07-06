<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Instance\Helpers\InstanceHelper;
use ZnCore\Text\Helpers\Inflector;
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

    protected function tab(int $repeat = 1): string
    {
        return str_repeat(' ', $repeat * 4);
    }

    /*protected function generateDiMethod(ClassGenerator $classGenerator, array $di) {
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('__construct');
        $methodBody = '';
        foreach ($di as $name => $type) {
            $parameterGenerator = new ParameterGenerator;
            $parameterGenerator->setName($name);
            $this->getFileGenerator()->setUse($type);
            $parameterGenerator->setType(basename($type));
            $methodGenerator->setParameter($parameterGenerator);
            $methodBody .= "\$this->{$name} = \${$name};\n";
            $property = new PropertyGenerator();
            $property->setVisibility(PropertyGenerator::VISIBILITY_PRIVATE);
            $property->setName($name);
            $classGenerator->addPropertyFromGenerator($property);
        }
        $methodGenerator->setBody($methodBody);
        $classGenerator->addMethodFromGenerator($diMethodGenerator);
    }*/

    protected function createGenerator(string $class): BaseScenario
    {
        $generator = InstanceHelper::create($class);
        $generator->name = $this->name;
        $generator->buildDto = $this->buildDto;
        $generator->domainNamespace = $this->domainNamespace;
        return $generator;
    }

    protected function isMakeInterface(): bool
    {
        return false;
    }

    public function getFileGenerator(): FileGenerator
    {
        if ($this->fileGenerator == null) {
            $this->fileGenerator = new FileGenerator();
            $this->fileGenerator->setClass($this->getClassGenerator());
        }
        return $this->fileGenerator;
    }

    public function setFileGenerator(FileGenerator $fileGenerator): void
    {
        $this->fileGenerator = $fileGenerator;
    }

    public function getClassGenerator(): ClassGenerator
    {
        if ($this->classGenerator == null) {
            $this->classGenerator = new ClassGenerator();
        }
        return $this->classGenerator;
    }

    public function setClassGenerator(ClassGenerator $classGenerator): void
    {
        $this->classGenerator = $classGenerator;
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
        return \ZnCore\Instance\Helpers\ClassHelper::getNamespace($this->domainNamespace);
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
        $interfaceGenerator = new InterfaceGenerator();
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
            $phpCode = str_replace('\\' . $useClass, \ZnCore\Instance\Helpers\ClassHelper::getClassOfClassName($useClass), $phpCode);
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
//        $fileGenerator->setClass($classGenerator);
        ClassHelper::generateFile($this->getFullClassName(), $fileGenerator->generate());
    }
}
