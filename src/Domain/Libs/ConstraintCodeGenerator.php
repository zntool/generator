<?php

namespace ZnTool\Generator\Domain\Libs;

use Zend\Code\Generator\FileGenerator;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnTool\Generator\Domain\Helpers\FieldRenderHelper;

class ConstraintCodeGenerator
{

    private $fileGenerator;

    public function __construct(FileGenerator $fileGenerator)
    {
        $this->fileGenerator = $fileGenerator;
    }

    public function generateCode($attribute): array
    {
        $validationRules = [];
        $attributeName = Inflector::variablize($attribute);
        $isInt = FieldRenderHelper::isMatchSuffix($attribute, '_id');
        if($isInt) {
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\Positive());";
        }

        $isStatus = $attribute == 'status_id';
        if($isStatus) {
            $this->fileGenerator->setUse(\ZnCore\Base\Enums\StatusEnum::class);
            $this->fileGenerator->setUse(\ZnCore\Domain\Constraints\Enum::class);
            $validationRules[] =
                "\$metadata->addPropertyConstraint('$attributeName', new Enum([
    'class' => StatusEnum::class,
]));";
        }

        $isBoolean = FieldRenderHelper::isMatchPrefix($attribute, 'is_');
        if($isBoolean) {
            $this->fileGenerator->setUse(\ZnCore\Domain\Constraints\Boolean::class);
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Boolean());";
        }

        $isCount = FieldRenderHelper::isMatchSuffix($attribute, '_count') || $attribute == 'size';
        if($isCount) {
            $validationRules[] = "\$metadata->addPropertyConstraint('$attributeName', new Assert\PositiveOrZero());";
        }
        return $validationRules;
    }
}
