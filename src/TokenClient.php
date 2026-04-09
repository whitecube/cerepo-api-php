<?php

namespace Whitecube\Cerepo;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

class TokenClient
{
    protected HttpClient $http;
    protected string $scope = 'https://cerepo.io/api/.default';

    protected ?string $accessToken = null;
    protected ?int $expiresAt = null;

    public function __construct(
        public readonly string $tokenUrl,
        public readonly string $clientId,
        public readonly string $clientSecret,
    ) {
        $this->http = new HttpClient();
    }

    public function getAccessToken(): string
    {
        if ($this->accessToken && $this->expiresAt && $this->expiresAt > time() + 60) {
            return $this->accessToken;
        }

        $response = $this->http->post($this->tokenUrl, [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->scope,
            ],
        ]);

        return $this->handleTokenResponse($response);
    }

    protected function handleTokenResponse(ResponseInterface $response): string
    {
        $data = json_decode((string) $response->getBody(), true);

        if (! isset($data['access_token'])) {
            throw new \RuntimeException('No access_token in token response');
        }

        $this->accessToken = $data['access_token'];
        $this->expiresAt = isset($data['expires_in'])
            ? time() + (int) $data['expires_in']
            : null;

        return $this->accessToken;
    }
}
