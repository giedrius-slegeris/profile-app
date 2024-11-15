<?php

namespace App\Service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerInterface;

class SlackNotifierService
{
    private string $webhookUrl;
    private Client $client;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->webhookUrl = getenv('SLACK_WEBHOOK') ?: '';
        $this->client = new Client();
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @return bool
     * @throws GuzzleException
     */
    public function sendMessage(string $message): bool
    {
        ignore_user_abort(true);

        $request = new Request(
            'POST',
            $this->webhookUrl,
            ['Content-Type' => 'application/json'],
            json_encode(['text' => $message])
        );

        try {
            $resp = $this->client->send($request);
        } catch (Exception $e) {
            $this->logger->error(sprintf("Failed to send slack contact message, %s", $e->getMessage()));
            return false;
        }
        return true;
    }
}