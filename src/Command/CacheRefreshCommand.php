<?php

declare(strict_types=1);

namespace App\Command;

use App\Bcr\Configuration;
use App\Bcr\Feed\Cache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function count;
use function date;
use function sprintf;

class CacheRefreshCommand extends Command
{
    protected static $defaultName = 'app:cache-refresh';

    private Configuration $configuration;
    private Cache $cache;
    private HttpClientInterface $httpClient;

    public function __construct(Configuration $configuration, Cache $cache, HttpClientInterface $httpClient)
    {
        parent::__construct();

        $this->configuration = $configuration;
        $this->cache         = $cache;
        $this->httpClient    = $httpClient;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->configuration->getAllFeeds($this->httpClient) as $feed) {
            $refreshInterval = $feed->getRefreshInterval();

            if ((int) date('i') % $refreshInterval !== 0) {
                continue;
            }

            $items = $feed->getList();

            if (! $items) {
                continue;
            }

            $this->cache->set($items, $feed->getHash(), 2 * $refreshInterval * 60);

            $io->success(sprintf('fetched %d items for feed %s', count($items), $feed->getHash()));
        }
    }
}
