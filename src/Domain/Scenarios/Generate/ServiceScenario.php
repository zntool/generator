<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\DocBlock\Tag\MethodTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use ZnCore\Text\Helpers\Inflector;
use ZnLib\Components\Store\StoreFile;
use ZnCore\EntityManager\Interfaces\EntityManagerInterface;
use ZnTool\Generator\Domain\Enums\TypeEnum;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Helpers\LocationHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class ServiceScenario extends BaseScenario
{

    public function typeName()
    {
        return 'Service';
    }

    public function classDir()
    {
        return 'Services';
    }

    /*protected function isMakeInterface(): bool
    {
        return true;
    }

    protected function createInterface()
    {

    }*/

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = $this->getFileGenerator();
        $classGenerator = $this->getClassGenerator();
        $classGenerator->setName($className);

        $serviceInterfaceScenario = $this->createGenerator(ServiceInterfaceScenario::class);
        $serviceInterfaceScenario->run();
        $interfaceFullName = $serviceInterfaceScenario->getFullClassName();
        $fileGenerator->setUse($interfaceFullName);
        $classGenerator->setImplementedInterfaces([$interfaceFullName]);


//        $repositoryInterfaceFullClassName = $this->buildDto->domainNamespace . LocationHelper::fullInterfaceName($this->name, TypeEnum::REPOSITORY);
//        $repositoryInterfacePureClassName = basename($repositoryInterfaceFullClassName);
//        $fileGenerator->setUse($repositoryInterfaceFullClassName);
        //$repositoryInterfaceClassName = basename($repositoryInterfaceFullClassName);


        $repositoryInterfaceScenario = $this->createGenerator(RepositoryInterfaceScenario::class);
        $repositoryInterfaceFullClassName = $repositoryInterfaceScenario->getFullClassName();
        $repositoryInterfacePureClassName = basename($repositoryInterfaceFullClassName);
        $fileGenerator->setUse($repositoryInterfaceFullClassName);

//        $classGenerator->addProperty('em', null, PropertyGenerator::FLAG_PRIVATE);
        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $classGenerator->addProperties([
                    [Inflector::variablize($attribute)]
                ]);
            }
        }
        $fileGenerator->setNamespace($this->classNamespace());
//        $fileGenerator->setClass($classGenerator);


        if ($this->buildDto->isCrudService) {
            $fileGenerator->setUse('ZnCore\Domain\Service\Base\BaseCrudService');
            $classGenerator->setExtendedClass('BaseCrudService');
        } else {
            $fileGenerator->setUse('ZnCore\Domain\Service\Base\BaseService');
            $classGenerator->setExtendedClass('BaseService');
        }

        $this->generateConstructMethod();

        $entityFullClassName = $this->domainNamespace . LocationHelper::fullClassName($this->name, TypeEnum::ENTITY);
        $entityPureClassName = \ZnCore\Base\Instance\Helpers\ClassHelper::getClassOfClassName($entityFullClassName);
        $fileGenerator->setUse($entityFullClassName);

        $methodGenerator = $this->generateGetEntityClassMethod($entityPureClassName);
        $classGenerator->addMethodFromGenerator($methodGenerator);


        $docBlockGenerator = new DocBlockGenerator();
        $methodTag = new MethodTag('getRepository()', ['\\' . $repositoryInterfacePureClassName]);
        $docBlockGenerator->setTag($methodTag);
        $classGenerator->setDocBlock($docBlockGenerator);

        /*$code = "
    public function __construct({$repositoryInterfaceClassName} \$repository)
    {
        \$this->repository = \$repository;
    }
";*/

        $phpCode = $this->generateFileCode($fileGenerator);

        $phpCode = str_replace("Interface\n * getRepository()", 'Interface getRepository()', $phpCode);

        /*$phpCode = $fileGenerator->generate();
        foreach ($fileGenerator->getUses() as $useItem) {
            $useClass = $useItem[0];
            $phpCode = str_replace('\\' . $useClass, \ZnCore\Base\Instance\Helpers\ClassHelper::getClassOfClassName($useClass), $phpCode);
        }*/


        //dd($phpCode);

        //$phpCode = str_replace('public function __construct(\\', 'public function __construct(', $phpCode);

        ClassHelper::generateFile($this->getFullClassName(), $phpCode);

        $this->updateContainerConfig($fileGenerator);
    }

    private function generateConstructMethod()
    {
        $this->getFileGenerator()->setUse(EntityManagerInterface::class);
        $parameterEm = new ParameterGenerator;
        $parameterEm->setName('em');
        $parameterEm->setType(EntityManagerInterface::class);

        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('__construct');
        $methodGenerator->setParameter($parameterEm);
//        $methodGenerator->setParameter($parameterGenerator2);
        $methodGenerator->setBody('$this->setEntityManager($em);' /*. PHP_EOL . '$this->repository = $repository;'*/);
        $this->getClassGenerator()->addMethods([$methodGenerator]);
    }

    private function updateContainerConfig(FileGenerator $fileGenerator)
    {
        $fullClassName = $this->getFullClassName();
        $containerFileName = PackageHelper::pathByNamespace($this->domainNamespace) . '/config/container.php';
        $storeFile = new StoreFile($containerFileName);
        $containerConfig = $storeFile->load();
        $containerConfig['singletons'][$this->getInterfaceFullName()] = $fullClassName;
        $storeFile->save($containerConfig);
    }

    private function generateGetEntityClassMethod(string $entityPureClassName): MethodGenerator
    {
        $tableName = "{$this->buildDto->domainName}_{$this->buildDto->name}";
        $methodBody = "return {$entityPureClassName}::class;";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('getEntityClass');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('string');
        return $methodGenerator;
    }
}
