# CloudSearch Wrapper
A wrapper for the AWS SDK regarding CloudSearch. Provides a nicer, less error prone interface than the Amazon SDK for
CloudSearch.

## Installation

```
composer require phoogkamer/cloudsearch-wrapper:0.1.*
```

## Example usage

```php
$client = new CloudSearchClient($endpoint, $key, $secret);

$query = new CloudSearchStructuredQuery();

//Adds a field (id:1)
$query->addField('id', 1);

//Will give max 15 results
$query->setSize(15);

//Currently $result is still the standard AWS SDK result
$result = $client->search($query);
```

In active development!
