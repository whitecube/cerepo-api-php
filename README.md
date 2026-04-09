# Cerepo PHP API client

This package provides a small PHP client around the Cerepo API.

## Installation

```
composer require whitecube/cerepo-api-php
```

## Configuration

Create a `TokenClient` instance.  

```php
use Whitecube\Cerepo\TokenClient;

$auth = new TokenClient(
    $tokenUrl,     // e.g. "https://token.cerepo.io/74ab26ad-6287-4bdc-8a6e-a770ae261d2c/oauth2/v2.0/token"
    $clientId,     // your client id
    $clientSecret  // your client secret
);
```

Then, create the main `Client` used to call the Cerepo API:

```php
use Whitecube\Cerepo\Client;

$client = new Client($auth, 'https://acc.cerepo.io/api/');
```

## Basic usage

### Sources

```php
// POST /sources - Add or update a Source
$response = $client->sources()->post([
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

```php
// GET /sources/{unf_id} - Get a specific source by unified-id (unf_id)
$source = $client->sources()->get('1234');
```
