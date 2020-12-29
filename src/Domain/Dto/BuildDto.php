<?php

namespace ZnTool\Generator\Domain\Dto;

class BuildDto
{

    public $domainName = '';
    public $domainNamespace;
    public $typeModule;
    public $moduleName = '';
    public $moduleNamespace;
    public $types;
    public $name;
    public $endpoint;
    public $attributes = [];
    public $driver;
    public $typeArray;
    public $isCrudService;
    public $isCrudRepository;
    public $isCrudController;
}
