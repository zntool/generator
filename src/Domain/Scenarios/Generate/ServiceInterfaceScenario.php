<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\MethodTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Reflection\DocBlockReflection;
use ZnCore\Base\Text\Helpers\Inflector;
use ZnLib\Components\Store\StoreFile;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\Service\Interfaces\CrudServiceInterface;
use ZnUser\Notify\Domain\Interfaces\Repositories\TransportRepositoryInterface;
use ZnTool\Generator\Domain\Enums\TypeEnum;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Helpers\LocationHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class ServiceInterfaceScenario extends BaseInterfaceScenario
{

    public function typeName()
    {
        return 'ServiceInterface';
    }

    public function classDir()
    {
        return 'Interfaces\\Services';
    }

    protected function createClass()
    {
        $fileGenerator = $this->getFileGenerator();
        $interfaceGenerator = $this->getClassGenerator();
        $interfaceGenerator->setName($this->getClassName());
        if ($this->buildDto->isCrudService) {
            $fileGenerator->setUse(CrudServiceInterface::class);
            $interfaceGenerator->setImplementedInterfaces(['CrudServiceInterface']);
        }
//        $fileGenerator->setNamespace($this->classNamespace());
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
//        $fileGenerator->setClass($interfaceGenerator);
        ClassHelper::generateFile($this->getFullClassName(), $fileGenerator->generate());
    }
}
