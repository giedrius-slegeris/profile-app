<?php

namespace App\Service;

use Exception;
use Grpc\ChannelCredentials;
use OpenWeatherMapStore\GetWeatherDataRequest;
use OpenWeatherMapStore\GetWeatherDataResponse;
use OpenWeatherMapStore\OpenWeatherMapStoreServerClient;
use Psr\Log\LoggerInterface;
use const Grpc\STATUS_OK;

class WeatherStoreMapService {

    protected LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getWeatherData(): array
    {
        $client = new OpenWeatherMapStoreServerClient('store:10060', [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);

        list($weatherResp, $status) = $client->GetWeatherData(new GetWeatherDataRequest())->wait();
        if ($status->code !== STATUS_OK) {
            $this->logger->error("getWeatherData failed, ".$status->details.', GRPC code: '.$status->code);
            return [$weatherResp, null];
        }

        if(!$weatherResp instanceof GetWeatherDataResponse) {
            return [null, 'Weather data unavailable'];
        }
        return [$weatherResp, null];
    }
}
