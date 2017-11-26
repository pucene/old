<?php

namespace Pucene\Tests\TestBundle\Command;

use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportJsonCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('test:export')
            ->addArgument('index', InputArgument::REQUIRED)
            ->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        $schema = new PuceneSchema($input->getArgument('index'));

        $queryBuilder = (new QueryBuilder($connection))
            ->select('document.id as id')
            ->addSelect('document.document as data')
            ->from($schema->getDocumentsTableName(), 'document');

        $content = [];
        foreach ($queryBuilder->execute() as $item) {
            $content[$item['id']] = json_decode($item['data'], true);
        }

        file_put_contents($input->getArgument('file'), json_encode($content));
    }
}
