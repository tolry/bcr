<?php

namespace App\Controller;

use Buzz\Browser;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_SearchResult;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthController extends Controller
{
    private static $clientId = '';
    private static $clientSecrect = '';

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function authInstagram(Request $request): Response
    {
        $this->logger->critical("authInstagram :: params " . json_encode($request->query->all()));

        $callbackUrl = str_replace(
            'http://',
            'https://',
            $this->generateUrl('auth_instagram', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $queryParameters = [
            'client_id' => self::$clientId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => 'public_content',
        ];
        $url = 'https://api.instagram.com/oauth/authorize/?' . http_build_query($queryParameters);

        if ($request->query->has('code')) {
            return $this->fetchPermanentToken($request->get('code'), $callbackUrl);
        }

        return $this->render('auth/auth-instagram.html.twig', ['link' => $url]);
    }

    private function fetchPermanentToken(string $token, string $callbackUrl): Response
    {
        $form = [
            'client_id' => self::$clientId,
            'client_secret' => self::$clientSecrect,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $callbackUrl,
            'code' => $token,
        ];
        $browser = new \Buzz\Browser();
        $response = $browser->submit(
            "https://api.instagram.com/oauth/access_token",
            $form
        );

        $this->logger->critical("authInstagram :: fetchPermanentToken " . $response->getContent());

        return new Response($response->getContent());
    }
}
