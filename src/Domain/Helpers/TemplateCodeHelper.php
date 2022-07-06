<?php

namespace ZnTool\Generator\Domain\Helpers;

use ZnCore\Text\Helpers\Inflector;
use ZnTool\Generator\Domain\Dto\BuildDto;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\ArrayRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\AuthorIdRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\CreatedAtRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\DescriptionRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\IdRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\MiscRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\SizeRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\StatusRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\TimeRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\TitleRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\TypeBooleanRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\TypeIntegerRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\TypeTimeRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\UpdatedAtRender;
use ZnTool\Generator\Domain\Libs\MigrationFieldRender\UserIdRender;

class TemplateCodeHelper
{

    public static function generateCrudWebRoutesConfig(BuildDto $buildDto, string $controllerClassName)
    {
        $tpl = file_get_contents(__DIR__ . '/../Templates/Web/config/routes.yaml');
        $tpl = str_replace('{{moduleName}}', $buildDto->moduleName, $tpl);
        $tpl = str_replace('{{name}}', $buildDto->name, $tpl);
        $tpl = str_replace('{{endpoint}}', $buildDto->endpoint, $tpl);
        $tpl = str_replace('{{controllerClassName}}', $controllerClassName, $tpl);
        return $tpl;
    }

    public static function generateCrudApiRoutesConfig(BuildDto $buildDto, string $controllerClassName)
    {
        $tpl = file_get_contents(__DIR__ . '/../Templates/Api/config/routes.yaml');
        $tpl = str_replace('{{moduleName}}', $buildDto->moduleName, $tpl);
        $tpl = str_replace('{{name}}', $buildDto->name, $tpl);
        $tpl = str_replace('{{endpoint}}', $buildDto->endpoint, $tpl);
        $tpl = str_replace('{{controllerClassName}}', $controllerClassName, $tpl);
        return $tpl;
    }

    public static function generateMigrationClassCode(string $className, array $attributes, string $tableName = ''): string
    {
        $fieldCode = self::generateAttributes($attributes);
        $code =
"class {$className} extends BaseCreateTableMigration
{

    protected \$tableName = '{$tableName}';
    protected \$tableComment = '';

    public function tableStructure(Blueprint \$table): void
    {
{$fieldCode}
    }
}";
        return $code;
    }

    private static function generateAttributes(array $attributes)
    {
        $fieldCode = '';
        $fields = [];
        $spaces = str_repeat(" ", 4 * 2);
        foreach ($attributes as $attributeName) {
            $fields[] = $spaces . self::generateField($attributeName);
        }
        $fieldCode = implode(PHP_EOL, $fields);
        return $fieldCode;
    }

    private static function runFieldRender(string $renderClass, string $attributeName): ?string
    {
        $renderInstance = new $renderClass;
        $renderInstance->attributeName = $attributeName;
        if ( ! $renderInstance->isMatch()) {
            return null;
        }
        return $renderInstance->run();
    }

    private static function generateField(string $attributeName): string
    {
        $attributeName = Inflector::underscore($attributeName);
        $renderClasses = [
            IdRender::class,
            CreatedAtRender::class,
            UpdatedAtRender::class,
            SizeRender::class,
            StatusRender::class,
            TitleRender::class,
            UserIdRender::class,
            AuthorIdRender::class,
            DescriptionRender::class,
            TypeTimeRender::class,
            TypeBooleanRender::class,
            TypeIntegerRender::class,
            ArrayRender::class,
        ];
        $renderClasses[] = MiscRender::class;
        foreach ($renderClasses as $renderClass) {
            $field = self::runFieldRender($renderClass, $attributeName);
            if ($field) {
                return $field;
            }
        }
        return $field;
    }

}
