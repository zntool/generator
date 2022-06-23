<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Base\Libs\Enum\Interfaces\GetLabelsInterface;
use ZnCore\Base\Libs\Text\Helpers\Inflector;
use ZnCore\Contract\Rbac\Interfaces\GetRbacInheritanceInterface;
use ZnTool\Generator\Domain\Helpers\ClassHelper;

class PermissionEnumScenario extends BaseScenario
{

    public function typeName()
    {
        return 'PermissionEnum';
    }

    public function classDir()
    {
        return 'Enums\Rbac';
    }

    protected function getClassName(): string
    {
        return Inflector::camelize($this->buildDto->domainName) . parent::getClassName();
    }

    protected function crudOperaions(): array
    {
        return [
            'crud' => 'Полный доступ',
            'all' => 'Просмотр списка',
            'one' => 'Просмотр записи',
            'create' => 'Создание',
            'update' => 'Редактирование',
            'delete' => 'Удаление',
            'restore' => 'Восстановление',
        ];
    }

    protected function generateConst(array $operations)
    {
        foreach ($operations as $operationName => $description) {
            $constName = strtoupper($operationName);
            $prefix = Inflector::camelize($this->buildDto->domainName) . Inflector::camelize($this->buildDto->name);
            $const = new PropertyGenerator();
            $const->setConst(true);
            $const->setName($constName);
            $const->setDefaultValue('o' . $prefix . Inflector::camelize($operationName));
            $this->getClassGenerator()->addPropertyFromGenerator($const);
        }
    }

    protected function generateGetLabelsMethod(array $operations)
    {
        $this->addInterface(GetLabelsInterface::class);

        $labels = '';
        foreach ($operations as $operationName => $description) {
            $constName = strtoupper($operationName);
            $prefix = Inflector::camelize($this->buildDto->domainName) . Inflector::camelize($this->buildDto->name);
            $labels .= $this->tab(1) . "self::{$constName} => '{$prefix}. {$description}'," . PHP_EOL;
        }

        $methodBody = "return [\n{$labels}];";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('getLabels');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setStatic(true);
        $this->getClassGenerator()->addMethodFromGenerator($methodGenerator);
    }

    protected function generateGetInheritance(array $operations)
    {
        $this->addInterface(GetRbacInheritanceInterface::class);

        $labels = '';
        foreach ($operations as $operationName => $description) {
            if ($operationName != 'crud') {
                $constName = strtoupper($operationName);
                $labels .= $this->tab(2) . "self::{$constName}," . PHP_EOL;
            }
        }
        $labels = trim($labels, PHP_EOL);

        $methodBody =
            "return [
    self::CRUD => [
{$labels}
    ],
];";

        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('getInheritance');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setStatic(true);
        $this->getClassGenerator()->addMethodFromGenerator($methodGenerator);
    }

    protected function createClass()
    {
        $className = $this->getClassName();
//        $fullClassName = $this->getFullClassName();
        $fileGenerator = $this->getFileGenerator();
        $classGenerator = $this->getClassGenerator();
        $classGenerator->setName($className);

        $operations = $this->crudOperaions();

        $this->generateConst($operations);
        $this->generateGetLabelsMethod($operations);
        $this->generateGetInheritance($operations);

        $fileGenerator->setNamespace($this->classNamespace());
//        $fileGenerator->setClass($classGenerator);

        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($this->getFullClassName(), $phpCode);
    }
}
