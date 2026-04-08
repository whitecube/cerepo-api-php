# Cerepo PHP API client

This package provides a small PHP client around the Cerepo API.

## Configuration

Create a `TokenClient` instance.  

```php
use Whitecube\Cerepo\TokenClient;

$tokenClient = new TokenClient(
    $tokenUrl,     // e.g. "https://example.com/oauth2/v2.0/token"
    $clientId,     // your client id
    $clientSecret  // your client secret
);
```

Then, create the main `Client` used to call the Cerepo API:

```php
use Whitecube\Cerepo\Client;

$client = new Client($tokenClient, 'https://acc.cerepo.io/api/');
```

## Basic usage

### GET requests

```php
// GET /sources
$sources = $client->get('sources');

// GET /sources/{id}
$source = $client->get('sources', '1234');
```

### POST requests

```php
$response = $client->post('sources', [
    'lang'   => 'en',
    'title'  => 'Example article',
    'url'    => 'https://example.com/article',
    'content'=> 'Long content here…',
    'prod_ids' => ['product-1'],
    'val_ids'  => ['value-1'],
    'aud_ids'  => ['audience-1'],
    ...
]);
```
