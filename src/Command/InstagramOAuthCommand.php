<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class InstagramOAuthCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:auth:instagram')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $clientId = '69f327caeaf6434e8b3c5a0bd8425dc8';
        $clientSecrect = '14a5460e6adf40afb939f126b9ce1731';
        $callbackUrl = 'http://localhost/instagram-oauth.html';

        $queryParameters = [
            'client_id' => $clientId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => 'public_content',
        ];
        $url = 'https://api.instagram.com/oauth/authorize/?' . http_build_query($queryParameters);

        // code 3f24d521072844fc9fa0c332589058a1
        $output->writeln($url);

        $question = new Question('what token did you receive?');

        $token = $helper->ask($input, $output, $question);

        $form = [
            'client_id' => $clientId,
            'client_secret' => $clientSecrect,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $callbackUrl,
            'code' => $token,
        ];
        $browser = new \Buzz\Browser();
        $response = $browser->submit(
            "https://api.instagram.com/oauth/access_token",
            $form
        );

        dump($response);
    }
}
