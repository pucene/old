<?php

namespace Pucene\Tests\TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\JsonDumpReader\JsonDumpFactory;

class ImportWikidataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('test:import:wikidata')
            ->addArgument('index', InputArgument::REQUIRED)
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('adapter', null, InputOption::VALUE_REQUIRED, '', 'pucene')
            ->addOption('count', null, InputOption::VALUE_REQUIRED, '', 1000);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $factory = new JsonDumpFactory();
        $dumpReader = $factory->newBz2DumpReader($input->getArgument('file'));

        $client = $this->getContainer()->get('pucene.' . $input->getOption('adapter') . '.client');
        $index = $client->get($input->getArgument('index'));

        $count = $input->getOption('count');
        $progressBar = new ProgressBar($output, $count);
        $progressBar->setFormat('debug');

        for ($i = 0; $i < $count; ++$i) {
            $item = json_decode($dumpReader->nextJsonLine(), true);

            if (!array_key_exists('en', $item['labels']) || !array_key_exists('en', $item['descriptions'])) {
                $progressBar->advance();

                continue;
            }

            $document = [
                'title' => $item['labels']['en']['value'],
                'description' => $item['descriptions']['en']['value'],
            ];

            $index->index($document, 'my_type', $item['id']);

            $progressBar->advance();
        }

        $index->optimize();

        $progressBar->finish();
    }
}
