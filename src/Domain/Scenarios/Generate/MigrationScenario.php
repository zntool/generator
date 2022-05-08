<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Illuminate\Database\Schema\Blueprint;
use Zend\Code\Generator\FileGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Base\Libs\FileSystem\Helpers\FileStorageHelper;
use ZnDatabase\Migration\Domain\Base\BaseCreateTableMigration;
use ZnTool\Generator\Domain\Helpers\TemplateCodeHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class MigrationScenario extends BaseScenario
{

    private $time;

    public function __construct()
    {
        $this->time = date('Y_m_d_His');
    }

    public function typeName()
    {
        return 'Migration';
    }

    public function classDir()
    {
        return 'Migrations';
    }

    protected function getClassName(): string
    {
        $tableName = Inflector::underscore($this->name);
        $className = "m_{$this->time}_create_{$tableName}_table";
        return $className;
    }

    protected function createClass()
    {
        $fileGenerator = new FileGenerator();

        $fileGenerator->setNamespace('Migrations');
        $fileGenerator->setUse(Blueprint::class);
        $fileGenerator->setUse(BaseCreateTableMigration::class);

        $tableName = $this->buildDto->domainName . '_' . $this->buildDto->name;
        $tableName = Inflector::underscore($tableName);
        $code = TemplateCodeHelper::generateMigrationClassCode($this->getClassName(), $this->buildDto->attributes, $tableName);

        $fileGenerator->setBody($code);
        $fileName = $this->getFileName();
        FileStorageHelper::save($fileName, $fileGenerator->generate());
    }

    private function getFileName()
    {
        $className = $this->getClassName();
        $dir = PackageHelper::pathByNamespace($this->buildDto->domainNamespace . '/' . $this->classDir());
        $fileName = $dir . '/' . $className . '.php';
        return $fileName;
    }
}
