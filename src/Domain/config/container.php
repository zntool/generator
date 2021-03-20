<?php

return [
    'definitions' => [

    ],
    'singletons' => [
        \ZnTool\Generator\Domain\Interfaces\Services\DomainServiceInterface::class => \ZnTool\Generator\Domain\Services\DomainService::class,
        \ZnTool\Generator\Domain\Interfaces\Services\ModuleServiceInterface::class => \ZnTool\Generator\Domain\Services\ModuleService::class,
    ],
];
