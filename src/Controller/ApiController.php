<?php

namespace App\Controller;

use App\Service\OutputHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ApiController extends AbstractController
{
    public function __construct(private readonly OutputHelper $outputHelper)
    {
    }

    /**
     * @Route("/api", name="api")
     */
    public function index(): Response
    {
        return $this->json(
            $this->outputHelper->generateOutput()
        );
    }
}
