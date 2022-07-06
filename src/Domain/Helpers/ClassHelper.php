<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\FileSystem\Helpers\FileStorageHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class ClassHelper
{

    public static function generateFile(string $alias, string $code)
    {
        $fileName = PackageHelper::pathByNamespace($alias);
        FileStorageHelper::save($fileName . '.php', $code);
    }
}
