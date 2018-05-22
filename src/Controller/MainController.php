<?php

namespace App\Controller;

use App\Bcr\Configuration;
use App\Bcr\Feed;
use DateTime;
use IntlDateFormatter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    private $logger;
    private $feed;

    public function __construct(Feed $feed, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->feed = $feed;
    }

    public function index(): Response
    {
        return $this->render('main/index.html.twig', []);
    }

    public function feed(): Response
    {
        $items = $this->feed->fetchItems();

        return new JsonResponse($this->groupByDate($items, 'published'));
    }

    private function groupByDate(array $items, $groupBy): array
    {
        $fmt = new IntlDateFormatter(
            'de_DE',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            'Europe/Berlin',
            IntlDateFormatter::GREGORIAN,
            'MMMM YYYY'
        );

        usort($items, function ($a, $b) use ($groupBy) {
            return $b->$groupBy <=> $a->$groupBy;
        });

        $groupedItems = [];
        foreach ($items as $item) {
            $group = $fmt->format($item->$groupBy);
            if (!isset($groupedItems[$group])) {
                $groupedItems[$group] = [
                    $groupBy => $group,
                    'items' => [],
                ];
            }

            $groupedItems[$group]['items'][] = $item;
        }

        return array_values($groupedItems);
    }
}
