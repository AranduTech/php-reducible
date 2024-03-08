# Reducible Trait

A trait to make your classes customizable via a set of reducers.

## Installation

```bash
composer require arandu/reducible
```

## Usage

```php
use Arandu\Reducible\Reducible;

class MyClass
{
    use Reducible;

    public function callApi($url, $options)
    {
        // will call all reducers for the method name
        $options = $this->transformCallApiOptions($options);

        // ...
    }
}

// -----

// Register a 'transformCallApiOptions' reducer
MyClass::reducer('transformCallApiOptions', function ($options) {
    return [
        ...$options,
        'headers' => [
            ...($options['headers'] ?? []),
            'Authorization' => 'Bearer ' . $this->token,
        ],
    ];
});

// Multiple reducers can be added
MyClass::reducer('transformCallApiOptions', function ($options) {
    return [
        ...$options,
        'headers' => [
            ...($options['headers'] ?? []),
            'X-Api-Key' => $this->apiKey,
        ],
    ];
});

// -----

$myClass = new MyClass();

$myClass->callApi('https://api.example.com', [
    'headers' => [
        'Content-Type' => 'application/json',
    ],
]);
// The callApi method will be called with the following options:
// [
//     'headers' => [
//         'Content-Type' => 'application/json',
//         'Authorization' => 'Bearer ' . $myClass->token,
//         'X-Api-Key' => $myClass->apiKey,
//     ],
// ]
```

## Advanced Usage

Read the [Advanced Usage](docs/advanced-usage.md) documentation.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Testing

```bash
composer test
```
