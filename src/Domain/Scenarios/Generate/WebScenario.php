<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnTool\Generator\Domain\Helpers\TemplateCodeHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

class WebScenario extends BaseScenario
{

    public function typeName()
    {
        return 'Controller';
    }

    public function classDir()
    {
        return 'Controllers';
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

        $classGenerator->addProperties([
            ['service', null, PropertyGenerator::FLAG_PRIVATE]
        ]);

        $fileGenerator->setUse('Symfony\Bundle\FrameworkBundle\Controller\AbstractController');
        $fileGenerator->setUse('ZnLib\Web\Symfony4\WebBundle\Traits\AccessTrait');

        $classGenerator->setExtendedClass('AbstractController');
        $classGenerator->addTrait('AccessTrait');


        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('service');
        $parameterGenerator->setType('ExampleService');

        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('__construct');
        $methodGenerator->setParameter($parameterGenerator);
        $methodGenerator->setBody('$this->service = $service;');
        $classGenerator->addMethods([$methodGenerator]);

        $fileGenerator->setNamespace($this->buildDto->moduleNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());

        $this->generateRouteConfig($fileGenerator->getNamespace() . '\\' . $className);
    }

    private function generateRouteConfig($classFullName)
    {
        $path = PackageHelper::pathByNamespace($this->buildDto->moduleNamespace);
        $routesConfigFile = $path . '/config/routes.yaml';
        FileHelper::save($routesConfigFile, TemplateCodeHelper::generateCrudWebRoutesConfig($this->buildDto, $classFullName));
    }

}
