<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Base\Legacy\Code\entities\ClassEntity;
use ZnCore\Base\Legacy\Code\entities\ClassUseEntity;
use ZnCore\Base\Legacy\Code\entities\ClassVariableEntity;
use ZnCore\Base\Legacy\Code\entities\DocBlockEntity;
use ZnCore\Base\Legacy\Code\entities\DocBlockParameterEntity;
use ZnCore\Base\Legacy\Code\entities\InterfaceEntity;
use ZnCore\Base\Legacy\Code\enums\AccessEnum;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Base\Libs\Store\StoreFile;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Service\CrudServiceInterface;
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

    protected function isMakeInterface(): bool
    {
        return true;
    }

    protected function createInterface()
    {
        $fileGenerator = new FileGenerator;
        $interfaceGenerator = new InterfaceGenerator;
        $interfaceGenerator->setName($this->getInterfaceName());
        if ($this->buildDto->isCrudService) {
            $fileGenerator->setUse(CrudServiceInterface::class);
            $interfaceGenerator->setImplementedInterfaces(['CrudServiceInterface']);
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        $fileGenerator->setClass($interfaceGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $this->getInterfaceName(), $fileGenerator->generate());
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
        $fileGenerator->setUse(EntityManagerInterface::class);

        $repositoryInterfaceFullClassName = $this->buildDto->domainNamespace . LocationHelper::fullInterfaceName($this->name, TypeEnum::REPOSITORY);
        $repositoryInterfacePureClassName = basename($repositoryInterfaceFullClassName);
        $fileGenerator->setUse($repositoryInterfaceFullClassName);
        //$repositoryInterfaceClassName = basename($repositoryInterfaceFullClassName);
        //$fileGenerator->setUse($repositoryInterfaceFullClassName);

//        $classGenerator->addProperty('em', null, PropertyGenerator::FLAG_PRIVATE);
        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $classGenerator->addProperties([
                    [Inflector::variablize($attribute)]
                ]);
            }
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);


        if ($this->buildDto->isCrudService) {
            $fileGenerator->setUse('ZnCore\Domain\Base\BaseCrudService');
            $classGenerator->setExtendedClass('BaseCrudService');
        } else {
            $fileGenerator->setUse('ZnCore\Domain\Base\BaseService');
            $classGenerator->setExtendedClass('BaseService');
        }

        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('em');
        $parameterGenerator->setType(EntityManagerInterface::class);

        $parameterGenerator2 = new ParameterGenerator;
        $parameterGenerator2->setName('repository');
        $parameterGenerator2->setType($repositoryInterfacePureClassName);

        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('__construct');
        $methodGenerator->setParameter($parameterGenerator);
        $methodGenerator->setParameter($parameterGenerator2);
        $methodGenerator->setBody('$this->setEntityManager($em);' . PHP_EOL . '$this->repository = $repository;');

        $classGenerator->addMethods([$methodGenerator]);

        $entityFullClassName = $this->domainNamespace . LocationHelper::fullClassName($this->name, TypeEnum::ENTITY);
        $entityPureClassName = \ZnCore\Base\Helpers\ClassHelper::getClassOfClassName($entityFullClassName);
        $fileGenerator->setUse($entityFullClassName);

        $methodGenerator = $this->generateGetEntityClassMethod($entityPureClassName);
        $classGenerator->addMethodFromGenerator($methodGenerator);

        /*$code = "
    public function __construct({$repositoryInterfaceClassName} \$repository)
    {
        \$this->repository = \$repository;
    }
";*/

        $phpCode = $this->generateFileCode($fileGenerator);


        /*$phpCode = $fileGenerator->generate();
        foreach ($fileGenerator->getUses() as $useItem) {
            $useClass = $useItem[0];
            $phpCode = str_replace('\\' . $useClass, \ZnCore\Base\Helpers\ClassHelper::getClassOfClassName($useClass), $phpCode);
        }*/


        //dd($phpCode);

        //$phpCode = str_replace('public function __construct(\\', 'public function __construct(', $phpCode);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $phpCode);

        $this->updateContainerConfig($fileGenerator);
    }

    private function updateContainerConfig(FileGenerator $fileGenerator) {
        $fullClassName = $this->getFullClassName();
        $containerFileName = PackageHelper::pathByNamespace($this->domainNamespace) . '/config/container.php';
        $storeFile = new StoreFile($containerFileName);
        $containerConfig = $storeFile->load();
        $containerConfig['singletons'][$this->getInterfaceFullName()] = $fullClassName;
        $storeFile->save($containerConfig);
    }

    private function generateGetEntityClassMethod(string $entityPureClassName): MethodGenerator {
        $tableName = "{$this->buildDto->domainName}_{$this->buildDto->name}";
        $methodBody = "return {$entityPureClassName}::class;";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('getEntityClass');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('string');
        return $methodGenerator;
    }
}
