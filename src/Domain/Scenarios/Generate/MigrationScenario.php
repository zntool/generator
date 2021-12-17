<?php

namespace ZnTool\Generator\Domain\Scenarios\Generate;

use Doctrine\Inflector\Inflector;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnTool\Generator\Domain\Helpers\TemplateCodeHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;
use Zend\Code\Generator\FileGenerator;

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
        $tableName = \ZnCore\Base\Legacy\Yii\Helpers\Inflector::underscore($this->name);
        $className = "m_{$timeStr}_create_{$tableName}_table";
        return $className;
    }

    protected function createClass()
    {

        $fileGenerator = $this->getFileGenerator();

        $fileGenerator->setNamespace('Migrations');
        $fileGenerator->setUse('Illuminate\Database\Schema\Blueprint');
        $fileGenerator->setUse('ZnLib\Migration\Domain\Base\BaseCreateTableMigration');

        $tableName = $this->buildDto->domainName . '_' . $this->buildDto->name;
        $tableName = \ZnCore\Base\Legacy\Yii\Helpers\Inflector::underscore($tableName);
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
