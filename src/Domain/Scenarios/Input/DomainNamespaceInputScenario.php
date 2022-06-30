<?php

namespace ZnTool\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use ZnCore\Base\Instance\Helpers\ClassHelper;
use ZnLib\Console\Symfony4\Question\ChoiceQuestion;
use ZnSandbox\Sandbox\Bundle\Domain\Entities\BundleEntity;
use ZnSandbox\Sandbox\Bundle\Domain\Interfaces\Services\BundleServiceInterface;
use ZnSandbox\Sandbox\Generator\Domain\Services\GeneratorService;

class DomainNamespaceInputScenario extends BaseInputScenario
{

    private $bundleService;

    public function __construct(
        BundleServiceInterface $bundleService
    )
    {
        $this->bundleService = $bundleService;
    }
    protected function paramName()
    {
        return 'domainNamespace';
    }

    protected function question(): Question
    {
        /** @var BundleEntity[] $bundleCollection */
        $bundleCollection = $this->bundleService->findAll();
        $domainCollection = [];
        $domainCollectionNamespaces = [];
        foreach ($bundleCollection as $bundleEntity) {
            if($bundleEntity->getDomain()) {
                $domainNamespace = ClassHelper::getNamespace($bundleEntity->getDomain()->getClassName());
                $domainName = $bundleEntity->getDomain()->getName();
                $title = "$domainName ($domainNamespace)";
                $domainCollection[] = $title;
                $domainCollectionNamespaces[$title] = $domainNamespace;
            }
            // dd($domainNamespace);
        }

        $question = new ChoiceQuestion(
            'Select domain',
            $domainCollection
        );
        return $question;
    }

   /* private function selectDomain(InputInterface $input, OutputInterface $output): string {

        $selectedDomain = $this->getHelper('question')->ask($input, $output, $question);
        $domainNamespace = $domainCollectionNamespaces[$selectedDomain];

        return $domainNamespace;
    }*/
}
