<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\FileSystem\Helpers\FileStorageHelper;
use ZnLib\Components\Store\Store;

class CodeHelper
{

    public static function generatePhpData($alias, $data)
    {
        $codeEntity = new CodeEntity();
        $codeEntity->fileName = $alias;
        $codeEntity->code = self::encodeDataForPhp($data);
        self::save($codeEntity);
    }

    public static function save(CodeEntity $codeEntity)
    {
        $pathName = $codeEntity->fileName;
        $fileName = $pathName . '.' . $codeEntity->fileExtension;
        $code = CodeHelper::render($codeEntity);
        FileStorageHelper::save($fileName, $code);
    }

    private static function render(CodeEntity $codeEntity)
    {
        $render = new CodeRender();
        $render->entity = $codeEntity;
        return $render->run();
    }

    private static function encodeDataForPhp($data)
    {
        $store = new Store('php');
        $content = $store->encode($data);
        $code = 'return ' . $content . ';';
        return $code;
    }
}
