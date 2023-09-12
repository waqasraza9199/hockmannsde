<?php

declare(strict_types=1);

namespace LZYT8\ApiMailer\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use LZYT8\ApiMailer\Service\ConfigurationService;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SendService {

    private LoggerInterface $logger;
    private ConfigurationService $configurationService;

    public function __construct(ConfigurationService $configurationService, LoggerInterface $logger) 
    {
        $this->logger = $logger;
        $this->configurationService = $configurationService;
    }

    public function send(array $payload) : int 
    {
        $service = $this->configurationService->getService();

        try
        {
            if ($service == "mailgun")
                $response = $this->sendViaMailgun($payload);
            elseif ($service == "external")
                $response = $this->sendViaExternal($payload);
            else
                throw new Exception("Selected service is not available");

            $statusCode = $response->getStatusCode();

            if ($statusCode != 200)
                $this->logger->error($response->getContent(false), ['service' => $service]);

            return $statusCode;
        }
        catch(Exception $e) {
            $this->logger->error('Request encountered an error');
            return 503;
        }
    }

    protected function sendViaMailgun(array $payload) : ResponseInterface
    {
        $headers = [];
        $body = new FormDataPart($payload);

        foreach ($body->getPreparedHeaders()->all() as $header) {
            $headers[] = $header->toString();
        }

        $endpoint = $this->configurationService->getBaseUrl() . '/messages';
        $response = HttpClient::create()->request('POST', $endpoint, [
            'auth_basic' => 'api:' . $this->configurationService->getApiKey(),
            'headers' => $headers,
            'body' => $body->bodyToIterable(),
            'max_duration' => 50
        ]);

        return $response;
    }

    protected function sendViaExternal(array $payload) : ResponseInterface 
    {
        $headers = [];
        $payload['smtp'] = $this->configurationService->getSMTPMailer();
        $body = new FormDataPart($payload);

        foreach ($body->getPreparedHeaders()->all() as $header) {
            $headers[] = $header->toString();
        }

        $endpoint = $this->configurationService->getSmtpServer() . '/messages';
        $response = HttpClient::create()->request('POST', $endpoint, [
            'auth_basic' => 'api:' . $this->configurationService->getLicenseKey(),
            'headers' => $headers,
            'body' => $body->bodyToIterable(),
            'max_duration' => 50
        ]);

        return $response;
    }
}