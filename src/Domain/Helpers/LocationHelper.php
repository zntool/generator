<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\Text\Helpers\Inflector;
use ZnTool\Generator\Domain\Enums\TypeEnum;

class LocationHelper
{

    static public $types = [
        TypeEnum::ENTITY => [
            'typeName' => 'Entity',
            'classDir' => 'Entities',
        ],
        TypeEnum::MIGRATION => [
            'typeName' => 'Migration',
            'classDir' => 'Migrations',
        ],
        TypeEnum::REPOSITORY => [
            'typeName' => 'Repository',
            'classDir' => 'Repositories',
        ],
        TypeEnum::SERVICE => [
            'typeName' => 'Service',
            'classDir' => 'Services',
        ],
        TypeEnum::INTERFACE => [
            'typeName' => 'Interface',
            'classDir' => 'Interfaces',
        ],
    ];

    public static function interfaceName(string $name, string $type)
    {
        return self::className($name, $type) . 'Interface';
    }

    public static function fullInterfaceName(string $name, string $type)
    {
        $classDir = self::$types[$type]['classDir'];
        return "\\Interfaces\\{$classDir}\\" . self::interfaceName($name, $type);
    }

    public static function className(string $name, string $type)
    {
        $cc = self::$types[$type];
        return Inflector::classify($name) . $cc['typeName'];
    }

    public static function fullClassName(string $name, string $type)
    {
        $classDir = self::$types[$type]['classDir'];
        return '\\' . $classDir . '\\' . self::className($name, $type);
    }

}
