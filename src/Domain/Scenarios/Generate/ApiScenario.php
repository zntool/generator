<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Base\Legacy\Code\entities\ClassEntity;
use ZnCore\Base\Legacy\Code\entities\ClassUseEntity;
use ZnCore\Base\Legacy\Code\entities\ClassVariableEntity;
use ZnCore\Base\Legacy\Code\entities\InterfaceEntity;
use ZnCore\Base\Legacy\Code\enums\AccessEnum;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnTool\Generator\Domain\Helpers\TemplateCodeHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class ApiScenario extends BaseScenario
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
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $classGenerator->setName($className);
        if ($this->isMakeInterface()) {
            $classGenerator->setImplementedInterfaces([$this->getInterfaceName()]);
            $fileGenerator->setUse($this->getInterfaceFullName());
        }

        if ($this->buildDto->isCrudController) {
            $fileGenerator->setUse('ZnLib\Rest\Symfony4\Base\BaseCrudApiController');
            $classGenerator->setExtendedClass('BaseCrudApiController');
        } else {
            $fileGenerator->setUse('Symfony\Bundle\FrameworkBundle\Controller\AbstractController');
            $classGenerator->setExtendedClass('AbstractController');
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
        FileHelper::save($routesConfigFile, TemplateCodeHelper::generateCrudApiRoutesConfig($this->buildDto, $classFullName));
    }
}
