<?php

declare(strict_types=1);

namespace App\Controller;

use App\Bcr\Feed;
use App\Bcr\Feed\ListItem;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function usort;

class MainController extends Controller
{
    private $logger;
    private $feed;

    public function __construct(Feed $feed, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->feed   = $feed;
    }

    public function index() : Response
    {
        return $this->render('main/index.html.twig', []);
    }

    public function feed() : Response
    {
        $items = $this->feed->fetchItems();

        usort(
            $items,
            static function (ListItem $a, ListItem $b) {
                                return $b->published <=> $a->published;
            }
        );

        return new JsonResponse($items);
    }
}
