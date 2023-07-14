<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TwitchController extends AbstractController
{
    /**
     * @Route("/from_twitch", name="from_twitch")
     */
    public function list(): Response
    {
        return $this->render('twitch/from_twitch.html.twig');
    }
}