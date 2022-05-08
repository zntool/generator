<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Base\Libs\FileSystem\Helpers\FileStorageHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class ClassHelper
{

    public static function generateFile(string $alias, string $code)
    {
        $fileName = PackageHelper::pathByNamespace($alias);
        FileStorageHelper::save($fileName . '.php', $code);
    }
}
