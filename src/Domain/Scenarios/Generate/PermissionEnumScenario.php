<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\MethodTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnTool\Generator\Domain\Enums\TypeEnum;
use ZnTool\Generator\Domain\Helpers\ClassHelper;
use ZnTool\Generator\Domain\Helpers\LocationHelper;
use ZnTool\Generator\Domain\Helpers\TemplateCodeHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;
use Zend\Code\Generator\FileGenerator;

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

    /*protected function getClassName(): string
    {
        $timeStr = date('Y_m_d_His');
        $className = "m_{$timeStr}_create_{$this->name}_table";
        return $className;
    }*/

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $classGenerator->setName($className);

        $classGenerator->setImplementedInterfaces([\ZnCore\Base\Interfaces\GetLabelsInterface::class]);
        $fileGenerator->setUse(\ZnCore\Base\Interfaces\GetLabelsInterface::class);


        $operations = [
            'all' => 'Просмотр списка',
            'one' => 'Просмотр записи',
            'create' => 'Создание',
            'update' => 'Редактирование',
            'delete' => 'Удаление',
            'restore' => 'Восстановление',
        ];

        $labels = '';
        foreach ($operations as $operationName => $description) {
            $prefix = Inflector::camelize($this->buildDto->domainName) . Inflector::camelize($this->buildDto->name);
            $constName = strtoupper($operationName);
            $const = new PropertyGenerator();
            $const->setConst(true);
            $const->setName($constName);
            $const->setDefaultValue('o' . $prefix . Inflector::camelize($operationName));
            $classGenerator->addPropertyFromGenerator($const);
            $labels .= "     self::{$constName} => '{$prefix}. {$description}'," . PHP_EOL;
        }

        $methodBody = "return [
{$labels}];";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('getLabels');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setStatic(true);
        $classGenerator->addMethodFromGenerator($methodGenerator);

        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);

        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $phpCode);
    }
}
