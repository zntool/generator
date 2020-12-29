<?php

namespace ZnTool\Generator\Domain\Helpers;

use php7rails\domain\BaseEntity;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use php7rails\domain\helpers\Helper;
use ZnCore\Base\Legacy\Code\entities\ClassEntity;
use ZnCore\Base\Legacy\Code\entities\ClassUseEntity;
use ZnCore\Base\Legacy\Code\entities\CodeEntity;
use ZnCore\Base\Legacy\Code\entities\InterfaceEntity;
use ZnCore\Base\Legacy\Code\render\ClassRender;
use ZnCore\Base\Legacy\Code\render\InterfaceRender;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnTool\Package\Domain\Helpers\PackageHelper;

class ClassHelper
{
	
	public static function classNameToFileName($class) {
		$alias = str_replace(['\\', '/'], SL, $class);
		return FileHelper::getAlias('@' . $alias);
	}

    public static function generateFile(string $alias, string $code) {
        $fileName = PackageHelper::pathByNamespace($alias);
	    FileHelper::save($fileName . '.php', $code);
    }

	public static function generate(BaseEntity $entity, $uses = []) {
		$codeEntity = new CodeEntity();
		$className = $entity->namespace . '\\' . $entity->name;
        $fileName = PackageHelper::pathByNamespace($className);
		/** @var ClassEntity|InterfaceEntity $entity */
		$codeEntity->fileName = $fileName;
		$codeEntity->namespace = $entity->namespace;
		$codeEntity->uses = Helper::forgeEntity($uses, ClassUseEntity::class);
		$codeEntity->code = self::render($entity);
		CodeHelper::save($codeEntity);
	}

    public static function render(BaseEntity $entity) {
		/** @var ClassRender|InterfaceRender $render */
		if($entity instanceof ClassEntity) {
			$render = new ClassRender();
		} elseif($entity instanceof InterfaceEntity) {
			$render = new InterfaceRender();
		}
		$render->entity = $entity;
		return $render->run();
	}
	
}
