<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class ClassHelper
{

    public static function generateFile(string $alias, string $code)
    {
        $fileName = PackageHelper::pathByNamespace($alias);
        FileHelper::save($fileName . '.php', $code);
    }
}
