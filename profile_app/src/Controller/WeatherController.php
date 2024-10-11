<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController {

    #[Route('/weather', name: 'weather_home', methods: ['GET'])]
    public function indexAction(): Response
    {
        return $this->render('weather/index.html.twig', []);
    }
}