<?php

namespace App\Service;

use Exception;
use Grpc\ChannelCredentials;
use OpenWeatherMapStore\GetWeatherDataRequest;
use OpenWeatherMapStore\GetWeatherDataResponse;
use OpenWeatherMapStore\OpenWeatherMapStoreServerClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use const Grpc\STATUS_OK;

/**
 * WeatherStoreMapService interfaces with the Golang service used to provide GRPC API data.
 */
class WeatherStoreMapService {

    protected LoggerInterface $logger;
    protected ParameterBagInterface $parameterBag;

    public function __construct(
        LoggerInterface $logger,
        ParameterBagInterface $parameterBag
    ) {
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getWeatherData(): array
    {
        $client = new OpenWeatherMapStoreServerClient(
            $this->parameterBag->get('open_weather_store_map.host').':'.$this->parameterBag->get('open_weather_store_map.port'),
            ['credentials' => ChannelCredentials::createInsecure()]
        );

        list($weatherResp, $status) = $client->GetWeatherData(new GetWeatherDataRequest())->wait();
        if ($status->code !== STATUS_OK) {
            $this->logger->error("getWeatherData failed, ".$status->details.', GRPC code: '.$status->code);
            return [null, 'Weather data unavailable at this time'];
        }

        if(!$weatherResp instanceof GetWeatherDataResponse) {
            return [null, 'Weather data unavailable at this time'];
        }
        return [$weatherResp, null];
    }
}
