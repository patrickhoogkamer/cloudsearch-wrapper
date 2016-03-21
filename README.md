# CloudSearch Wrapper
A wrapper for the AWS SDK regarding CloudSearch. Provides a nicer, less error prone interface than the Amazon SDK for
CloudSearch. Version 0.3.0 should be considered stable. I'm just not sure if the featureset is complete enough for a major version change.

## Installation

```
composer require phoogkamer/cloudsearch-wrapper:0.3.*
```

## Example usage

Below is a simple search example.

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

You will most probably need something more advanced, like with And and Or statements. This is done like so:

```php
$query->addAnd(function(CloudSearchStructuredQuery $query)
{
    $query->addOr(function(CloudSearchStructuredQuery $query)
    {
        //Add string by setting the second parameter true
        $query->addField('title', 'Forged Alliance', true);

        $query->addField('title', 'Supreme Commander', true);
    });

    $query->addOr(function(CloudSearchStructuredQuery $query)
    {
        $query->addField('id', 1);

        //Gets everything within a range from 2 to 5
        $query->addRangeField('id', 2, 5);
    });
});
```

Note that it's easy to nest in an elegant way.

In active development!
