<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Illuminate\Database\Schema\Blueprint;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnLib\Migration\Domain\Base\BaseCreateTableMigration;
use ZnTool\Generator\Domain\Helpers\TemplateCodeHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;
use Zend\Code\Generator\FileGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;

class MigrationScenario extends BaseScenario
{

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
        $timeStr = date('Y_m_d_His');
        $tableName = Inflector::underscore($this->name);
        $className = "m_{$timeStr}_create_{$tableName}_table";
        return $className;
    }

    protected function createClass()
    {

        $fileGenerator = $this->getFileGenerator();

        $fileGenerator->setNamespace('Migrations');
        $fileGenerator->setUse(Blueprint::class);
        $fileGenerator->setUse(BaseCreateTableMigration::class);

        $tableName = $this->buildDto->domainName . '_' . $this->buildDto->name;
        $tableName = Inflector::underscore($tableName);
        $code = TemplateCodeHelper::generateMigrationClassCode($this->getClassName(), $this->buildDto->attributes, $tableName);

        $fileGenerator->setBody($code);
        $fileName = $this->getFileName();
        FileHelper::save($fileName, $fileGenerator->generate());
    }

    private function getFileName()
    {
        $className = $this->getClassName();
        $dir = PackageHelper::pathByNamespace($this->buildDto->domainNamespace . '/' . $this->classDir());
        $fileName = $dir . '/' . $className . '.php';
        return $fileName;
    }

}
