<?php

namespace ZnTool\Generator\Domain\Libs;

use Zend\Code\Generator\FileGenerator;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;
use ZnTool\Generator\Domain\Helpers\TypeAttributeHelper;
use ZnTool\Generator\Domain\Libs\Types\ArrayType;
use ZnTool\Generator\Domain\Libs\Types\BaseType;
use ZnTool\Generator\Domain\Libs\Types\BoolType;
use ZnTool\Generator\Domain\Libs\Types\I18nType;
use ZnTool\Generator\Domain\Libs\Types\IntPositiveOrZeroType;
use ZnTool\Generator\Domain\Libs\Types\IntPositiveType;
use ZnTool\Generator\Domain\Libs\Types\IntType;
use ZnTool\Generator\Domain\Libs\Types\StatusIdType;

class ConstraintCodeGenerator
{

    private $fileGenerator;

    public function __construct(FileGenerator $fileGenerator)
    {
        $this->fileGenerator = $fileGenerator;
    }

    /*public function getTypes(string $attributeName) {
        $typeClasses = [
            IntType::class,
            IntPositiveType::class,
            IntPositiveOrZeroType::class,
            BoolType::class,
        ];
        $types = [];
        foreach ($typeClasses as $typeClass) {

        }
    }*/

    public function generateCode(string $attribute): array
    {
        $validationRules = [];
        $attributeName = Inflector::variablize($attribute);
//        $isInt = FieldRenderHelper::isMatchSuffix($attribute, '_id');

        if(IntPositiveType::match($attributeName)) {
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\Positive());";
        }

        /*$isTime = FieldRenderHelper::isMatchSuffix($attribute, '_id');
        if($isTime) {
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\DateTime());";
        }*/

//        $isStatus = $attribute == 'status_id';
        if(StatusIdType::match($attributeName)) {
            $this->fileGenerator->setUse(\ZnCore\Base\Enums\StatusEnum::class);
            $this->fileGenerator->setUse(\ZnCore\Base\Libs\Enum\Constraints\Enum::class);
            $validationRules[] =
                "\$metadata->addPropertyConstraint('$attributeName', new Enum([
    'class' => StatusEnum::class,
]));";
        }

        //$isBoolean = FieldRenderHelper::isMatchPrefix($attribute, 'is_');
        if(BoolType::match($attributeName)) {
            $this->fileGenerator->setUse(\ZnCore\Base\Libs\Validation\Constraints\Boolean::class);
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Boolean());";
        }

        //$isCount = FieldRenderHelper::isMatchSuffix($attribute, '_count') || $attribute == 'size';
        if(IntPositiveOrZeroType::match($attributeName)) {
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\PositiveOrZero());";
        }

        if(ArrayType::match($attributeName) || I18nType::match($attributeName)) {
            $this->fileGenerator->setUse(\ZnCore\Base\Libs\Arr\Constraints\Arr::class);
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Arr());";
        }

        return $validationRules;
    }
}

/*
id
user_id
title_i18n
name
group_name
group_title
description
like_count
store_size
is_accepted
has_child
props
group_props
status_id
created_at
updated_at
expired_at


 */