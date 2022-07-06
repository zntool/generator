<?php

namespace ZnTool\Generator;

use ZnCore\Bundle\Base\BaseBundle;

class Bundle extends BaseBundle
{

    public function deps(): array
    {
        return [
            new \ZnSandbox\Sandbox\Bundle\Bundle(['all']),
        ];
    }

    public function console(): array
    {
        return [
            'ZnTool\Generator\Commands',
        ];
    }

    public function container(): array
    {
        return [
            __DIR__ . '/Domain/config/container.php',
        ];
    }
}
