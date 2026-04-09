<?php

namespace Whitecube\Cerepo\Endpoints;

use Whitecube\Cerepo\Client;
use Whitecube\Cerepo\Source;

class Sources
{
    public function __construct(protected Client $client) {}

    public function get(string $id): array
    {
        $uri = 'sources/' . $id;

        return $this->client->request('GET', $uri);
    }

    public function post(array $data): mixed
    {
        $source = (new Source($data));

        return $this->client->request('POST', 'sources', $source->toArray());
    }
}
