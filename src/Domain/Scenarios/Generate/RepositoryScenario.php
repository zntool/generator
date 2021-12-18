<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Base\Libs\Store\StoreFile;
use ZnCore\Domain\Interfaces\Repository\CrudRepositoryInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnLib\Db\Base\BaseEloquentCrudRepository;
use ZnLib\Db\Base\BaseEloquentRepository;
use ZnTool\Generator\Domain\Enums\TypeEnum;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Helpers\LocationHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class RepositoryScenario extends BaseScenario
{

    public $driver;

    public function typeName()
    {
        return 'Repository';
    }

    public function classDir()
    {
        return 'Repositories';
    }

    protected function isMakeInterface(): bool
    {
        return true;
    }

    protected function createInterface()
    {
        $fileGenerator = new FileGenerator();
        $interfaceGenerator = new InterfaceGenerator;
        $interfaceGenerator->setName($this->getInterfaceName());
        if ($this->buildDto->isCrudRepository) {
            $fileGenerator->setUse(CrudRepositoryInterface::class);
            $interfaceGenerator->setImplementedInterfaces(['CrudRepositoryInterface']);
        } else {
            $fileGenerator->setUse(RepositoryInterface::class);
            $interfaceGenerator->setImplementedInterfaces(['RepositoryInterface']);
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        $fileGenerator->setClass($interfaceGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $this->getInterfaceName(), $fileGenerator->generate());
    }

    protected function createClass()
    {
        foreach ($this->buildDto->driver as $driver) {
            $this->createOneClass($driver);
        }
    }

    /* public function getFullClassName(): string
     {
         return $this->classNamespace() . '\\' . $this->getClassName();
     }*/

    protected function createOneClass(string $driver)
    {
        $className = $this->getClassName();
        $driverDirName = Inflector::camelize($driver);
        $repoClassName = $driverDirName . '\\' . $className;
        $fileGenerator = new FileGenerator();
        $classGenerator = new ClassGenerator();
        $fileGenerator->setClass($classGenerator);
        $fileGenerator->setNamespace($this->classNamespace() . '\\' . $driverDirName);

        $parentClass = $this->parentClass($driver);
        if ($parentClass) {
            $fileGenerator->setUse($parentClass);
            $classGenerator->setExtendedClass(basename($parentClass));
        }

        $methodGenerator = $this->generateTableNameMethod();
        $classGenerator->addMethodFromGenerator($methodGenerator);

        $entityFullClassName = $this->domainNamespace . LocationHelper::fullClassName($this->name, TypeEnum::ENTITY);
        $entityPureClassName = \ZnCore\Base\Helpers\ClassHelper::getClassOfClassName($entityFullClassName);
        $fileGenerator->setUse($entityFullClassName);

        $methodGenerator = $this->generateGetEntityClassMethod($entityPureClassName);
        $classGenerator->addMethodFromGenerator($methodGenerator);

        $classGenerator->setName($className);
        if ($this->isMakeInterface()) {
            $classGenerator->setImplementedInterfaces([$this->getInterfaceName()]);
            $fileGenerator->setUse($this->getInterfaceFullName());
        }


        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $this->getClassName(), $phpCode);

        $this->updateContainerConfig($fileGenerator);
    }

    private function updateContainerConfig(FileGenerator $fileGenerator)
    {
        $fullClassName = $this->getFullClassName();
        $className = $this->getClassName();
        $containerFileName = PackageHelper::pathByNamespace($this->domainNamespace) . '/config/container.php';
        $storeFile = new StoreFile($containerFileName);
        $containerConfig = $storeFile->load();
        $containerConfig['singletons'][$this->getInterfaceFullName()] = $fileGenerator->getNamespace() . '\\' . $className;
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

    private function generateTableNameMethod(): MethodGenerator
    {
        $tableName = "{$this->buildDto->domainName}_{$this->buildDto->name}";
        $tableName = Inflector::underscore($tableName);
        $methodBody = "return '{$tableName}';";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('tableName');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('string');
        return $methodGenerator;
    }

    private function parentClass($driver)
    {
        $className = '';
        if ('eloquent' == $driver) {
            if ($this->buildDto->isCrudRepository) {
                $className = BaseEloquentCrudRepository::class;
            } else {
                $className = BaseEloquentRepository::class;
            }
        } else {
            //$className = 'ZnCore\Domain\Base\BaseRepository';
        }
        return $className;
    }

}
