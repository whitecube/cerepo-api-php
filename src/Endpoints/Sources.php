<?php

namespace Whitecube\Cerepo\Endpoints;

use Whitecube\Cerepo\Client;
use Whitecube\Cerepo\Source;

class Sources
{
    public function __construct(protected Client $client) {}

    public function get(string $id): Source
    {
        $uri = 'sources/' . $id;

        $data = $this->client->request('GET', $uri);

        return (new Source($data));
    }

    public function post(array $data): mixed
    {
        $source = (new Source($data));

        return $this->client->request('POST', 'sources', $source->toArray());
    }
}
