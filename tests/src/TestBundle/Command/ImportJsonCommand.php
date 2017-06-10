<?php

namespace Pucene\Tests\TestBundle\Command;

use Pucene\Component\Client\ClientInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TODO add description here.
 */
class ImportJsonCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('test:import:json')
            ->addArgument('index', InputArgument::REQUIRED)
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('adapter', null, InputOption::VALUE_REQUIRED, '', 'pucene');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ClientInterface $client */
        $client = $this->getContainer()->get('pucene.' . $input->getOption('adapter') . '.client');
        $index = $client->get($input->getArgument('index'));

        $content = json_decode(file_get_contents($input->getArgument('file')), true);

        $progressBar = new ProgressBar($output, count($content));
        $progressBar->setFormat('debug');

        foreach ($content as $id => $item) {
            $index->index($item, 'my_type', $id);

            $progressBar->advance();
        }

        $index->optimize();

        $progressBar->finish();
    }
}
