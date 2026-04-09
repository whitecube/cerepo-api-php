<?php

namespace Whitecube\Cerepo;

use GuzzleHttp\Client as HttpClient;
use Whitecube\Cerepo\Source;
use Psr\Http\Message\ResponseInterface;

class Client
{
    protected HttpClient $http;
    protected TokenClient $tokenClient;
    protected string $baseUrl;

    public function __construct(TokenClient $tokenClient, string $baseUrl = 'https://acc.cerepo.io/api/')
    {
        $this->tokenClient = $tokenClient;
        $this->baseUrl = rtrim($baseUrl, '/') . '/';

        $this->http = new HttpClient([
            'base_uri' => $this->baseUrl,
        ]);
    }

    public function get(string $path, ?string $id = null): array
    {
        $uri = rtrim($path, '/') . ($id ? "/{$id}" : '');

        return $this->request('GET', $uri);
    }

    public function post(string $path, array $data)
    {
        $source = (new Source($data));

        return $this->request('POST', $path, $source->toArray());
    }

    public function request(string $method, string $uri, ?array $payload = null): mixed
    {
        $token = $this->tokenClient->getAccessToken();

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        if ($payload !== null && ! in_array(strtoupper($method), ['GET', 'HEAD'], true)) {
            $options['json'] = $payload;
        }

        $response = $this->http->request($method, $uri, $options);

        return $this->handleResponse($response);
    }

    protected function handleResponse(ResponseInterface $response): mixed
    {
        $status = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException("CeRepo API error: {$status} - {$body}");
        }

        $data = json_decode($body, true);

        return $data;
    }
}
