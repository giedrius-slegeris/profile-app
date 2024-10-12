<?php

namespace App\Controller;

use App\Service\WeatherStoreMapService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController {

    /**
     * @param WeatherStoreMapService $weatherStoreMapService
     * @param LoggerInterface $logger
     * @return Response
     */
    #[Route('/weather', name: 'weather_home', methods: ['GET'])]
    public function indexAction(
        WeatherStoreMapService $weatherStoreMapService,
        LoggerInterface $logger
    ): Response
    {
        $error = '';
        $data = null;

        try {
            list($weatherData, $err) = $weatherStoreMapService->getWeatherData();
            if(!is_null($err)){
                $error = $err;
            } else {
                $data = $weatherData;
            }
        } catch (Exception $e) {
            $logger->error($e->getMessage());
            $error = 'Unable to get weather data at this time. Please try again later.';
        }

        return $this->render('weather/index.html.twig', [
            'data' => $data,
            'error' => $error
        ]);
    }
}