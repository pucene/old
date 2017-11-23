<?php

namespace Pucene\Tests\TestBundle\Command;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadWikidataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('test:download:wikidata')->addArgument('file', InputArgument::REQUIRED)->addOption(
            'adapter',
            null,
            InputOption::VALUE_REQUIRED,
            '',
            'pucene'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $content = json_decode(file_get_contents($input->getArgument('file')), true);
        $client = new Client(['base_uri' => 'https://www.wikidata.org']);

        $progressBar = new ProgressBar($output, count($content));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $newData = [];
        foreach ($content as $id => $item) {
            try {
                $response = $client->get('/wiki/Special:EntityData/' . $id . '.json');
            } catch (\Exception $e) {
                continue;
            }

            $response = json_decode($response->getBody()->getContents(), true);
            $response = array_values($response['entities']);
            $response = reset($response);

            $title = $item['title'];
            if (array_key_exists('en', $response['labels'])) {
                $title = $response['labels']['en']['value'];
            }

            $description = $item['description'];
            if (array_key_exists('en', $response['descriptions'])) {
                $description = $response['descriptions']['en']['value'];
            }

            $newData[$response['id']] = [
                'title' => $title,
                'rawTitle' => $title,
                'description' => $description,
                'modified' => $response['modified'],
                'pageId' => (int)$response['pageid'],
                'seed' => rand(0, 100) / 100.0,
                'enabled' => rand(0, 1) === 1 ? true : false,
            ];

            $progressBar->advance();
        }

        file_put_contents($input->getArgument('file'), json_encode($newData));

        $progressBar->finish();
    }
}
