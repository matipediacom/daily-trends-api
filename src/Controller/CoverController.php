<?php

namespace App\Controller;

use App\Service\Feeds\FeedsHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final class CoverController extends AbstractController
{
    public function __construct(private readonly FeedsHandler $feedsHandler)
    {
    }

    #[Route('/', name: 'app_cover')]
    public function index(): Response
    {
        // TODO: Create custom error page or show zero Feeds if there is an error?

        try {
            $trendingFeeds = $this->feedsHandler->getTrendingFeeds(['el_pais', 'el_mundo']);
        } catch (Throwable) {
            $trendingFeeds = [];
        }

        return $this->render('cover/index.html.twig', [
            'trendingFeeds' => $trendingFeeds,
        ]);
    }
}
