<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use ZnCore\Text\Helpers\Inflector;
use ZnLib\Rpc\Rpc\Base\BaseCrudRpcController;
use ZnTool\Generator\Domain\Helpers\ClassHelper;

class RpcControllerScenario extends BaseScenario
{

    public function typeName()
    {
        return 'Controller';
    }

    public function classDir()
    {
        return 'Controllers';
    }

    public function classNamespace(): string
    {
        return $this->bundleNamespace() . '\\Rpc\\' . $this->classDir();
    }

    protected function generateDiMethod(ClassGenerator $classGenerator, array $di): MethodGenerator
    {
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('__construct');
        $methodBody = '';
        foreach ($di as $name => $type) {
            $parameterGenerator = new ParameterGenerator;
            $parameterGenerator->setName($name);
            $this->getFileGenerator()->setUse($type);
            $parameterGenerator->setType(basename($type));
//            $parameterGenerator->setType(\ZnCore\Instance\Helpers\ClassHelper::getClassOfClassName($type));
//            $fileGenerator->setUse($type);
            $methodGenerator->setParameter($parameterGenerator);
            $methodBody .= "\$this->{$name} = \${$name};\n";

            $property = new PropertyGenerator();
            $property->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED);
            $property->setName($name);
            $classGenerator->addPropertyFromGenerator($property);
        }
        $methodGenerator->setBody($methodBody);
        return $methodGenerator;
    }

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = $this->getFileGenerator();
        $classGenerator = $this->getClassGenerator();
        $classGenerator->setName($className);
//        $fileGenerator->setClass($classGenerator);

        $classGenerator->setExtendedClass(BaseCrudRpcController::class);
        $fileGenerator->setUse(BaseCrudRpcController::class);

        $serviceGenerator = $this->createGenerator(ServiceScenario::class);
        $serviceInterfaceName = $serviceGenerator->getInterfaceFullName();

        $di = [
            'service' => $serviceInterfaceName,
        ];
        $diMethodGenerator = $this->generateDiMethod($classGenerator, $di);
        $classGenerator->addMethodFromGenerator($diMethodGenerator);

        $this->generateAllowRelationsMethod();

        $fileGenerator->setNamespace($this->classNamespace());

        $phpCode = $this->generateFileCode($fileGenerator);

        ClassHelper::generateFile($this->getFullClassName(), $phpCode);
        $this->generateRoutes();
    }

    protected function generateAllowRelationsMethod()
    {
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('allowRelations');
        $methodGenerator->setReturnType('array');
        $methodGenerator->setBody('return [];');
        $this->getClassGenerator()->addMethodFromGenerator($methodGenerator);
    }

    protected function generateRouteCode(array $operationData, string $enumClass): string
    {
        $methodName = Inflector::variablize($this->buildDto->domainName) . Inflector::camelize($this->name);
        $enumConstName = strtoupper($operationData['permissionName']);
        return
            "[
        'method_name' => '{$methodName}.{$operationData['rpcName']}',
        'version' => '1',
        'is_verify_eds' => false,
        'is_verify_auth' => true,
        'permission_name' => {$enumClass}::{$enumConstName},
        'handler_class' => {$this->getClassName()}::class,
        'handler_method' => '{$operationData['actionName']}',
        'status_id' => 100,
        'title' => null,
        'description' => null,
    ],";
    }

    protected function generateRoutes()
    {
        $fileGenerator = new FileGenerator();
        $fileGenerator->setUse($this->getFullClassName());

        $permissionEnumScenario = $this->createGenerator(PermissionEnumScenario::class);

        $fileGenerator->setUse($permissionEnumScenario->getFullClassName());

        $methodName = Inflector::variablize($this->buildDto->domainName) . Inflector::camelize($this->name);

        $operations = [
            [
                'rpcName' => 'all',
                'permissionName' => 'all',
                'actionName' => 'all',
            ],
            [
                'rpcName' => 'oneById',
                'permissionName' => 'one',
                'actionName' => 'oneById',
            ],
            [
                'rpcName' => 'create',
                'permissionName' => 'create',
                'actionName' => 'add',
            ],
            [
                'rpcName' => 'update',
                'permissionName' => 'update',
                'actionName' => 'update',
            ],
            [
                'rpcName' => 'delete',
                'permissionName' => 'delete',
                'actionName' => 'delete',
            ],

        ];

        $body = '';
        foreach ($operations as $operationData) {
            $body .= "    " . $this->generateRouteCode($operationData, $permissionEnumScenario->getClassName()) . PHP_EOL;
        }

        $fileGenerator->setBody(
            "return [
$body
];"
        );

        $phpCode = $this->generateFileCode($fileGenerator);
        ClassHelper::generateFile($this->bundleNamespace() . '\\Rpc\\config\\' . $this->name . '-routes', $phpCode);
    }
}
