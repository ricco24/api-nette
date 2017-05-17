# Api Nette

Highly customizable and easy to setup REST api handling for Nette framework.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ricco24/api-nette/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ricco24/api-nette/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ricco24/api-nette/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ricco24/api-nette/?branch=master)
[![Build Status](https://travis-ci.org/ricco24/api-nette.svg?branch=master)](https://travis-ci.org/ricco24/api-nette)
[![Packagist](https://img.shields.io/packagist/v/kelemen/api-nette.svg?maxAge=14400)](https://packagist.org/packages/kelemen/api-nette)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3f401779-98cc-4191-8339-8fa211c917f5/big.png)](https://insight.sensiolabs.com/projects/3f401779-98cc-4191-8339-8fa211c917f5)

## Installation

```
composer require kelemen/api-nette
```

## Prepare to use

1. First of all you need an Api presenter for handle api requests. You can use `Kelemen\ApiNette\Presenter\ApiPresenter` or write you own.
2. Register new mapping in config.neon
    
    ```php
    application:
        mapping:
            Api: Kelemen\ApiNette\Presenter\*Presenter
    ```

3. Add api route to router. We use keyword **api** for identify api requests.

    ```php
    $router[] = new Route("api/<params .*>", [
        'presenter' => 'Api',
        'action' => 'default'   
    ]);
    ```

4. Configure Api (example from config.neon)

    ```php
    services:
        - Kelemen\ApiNette\Logger\Storage\DummyLoggerStorage
        - Kelemen\ApiNette\Logger\Logger
        api:
            class: Kelemen\ApiNette\Api
            setup:
                - get('users', 'Custom\Users\ListHandler')
                - get('users/{id}', 'Custom\Users\DetailHandler')
                - put('users/{id}', 'Custom\Users\CrateHandler', [middleware: ['Custom\Auth\Bearer'])
                - post('users/{id}', 'Custom\Users\UpdateHandler', [middleware: ['Custom\Auth\Bearer']])
    ```

## Api routes

### Add routes to api

REST api routes can be defined with shortcut functions (for most used HTTP methods):
- get
- post
- put 
- patch
- delete
- options

Or you can add any HTTP method processing with `add($method, $pattern, $handler, $params = [])` function.
```php
$api = new Api(...);
$api->add('purge', 'purge/urls', 'Handlers\PurgeHandler')
```

### Route patterns

In route pattern you can use replacements closed in **{** and **}**.
```php
$api = new Api(...);
$api->get('users', 'Handler');         // exact match for (with our route) /api/users
$api->get('users/{id}', 'Handler');    // parse parameter id from routes like /api/users/10, /api/users/sdk-2323 etc.
$api->get('users/{id}/message/{messageId}', 'Handler') // parse parameters id and messageId from matched requests
```

### Route handlers

Route definition use **lazy loading** from Nette DI Container. Handlers are defined by **type** or by **name** registered in config.neon file. For name definition, prefix service name with **#**. 
```php
$api = new Api(...);
$api->get('users', 'Full\Namespace\For\Handler');       // By type
$api->get('users/{id}', '#registeredHandlerName');      // By name
```

### Route parameters

As parameter is now accepted only **middleware** key. Middleware definition use same **lazy loading** logic as handlers.
```php
$api = new Api(...);
$api->get('users', 'Full\Namespace\For\Handler', ['middleware' => [
    'Middleware\Auth\Bearer',   // By type
    '#bearerAuthorization'      // By name
]]);
```

## Handler

Handler provide business logic for resolved api route.

```php
use Nette\Http\Request;
use Nette\Http\Response;
use Kelemen\ApiNette\Handler\BaseHandler;
use Kelemen\ApiNette\Response\JsonApiResponse;
use Kelemen\ApiNette\Validator\Validation;

class UserGetHandler extends BaseHandler
{
    // Here we can define validation rules for input parameters (see section Validations below).
    // This function is optional.
    public function validate()
    {
        return [
            new Validation('path', 'id', 'required|integer'),
            new Validation('get', 'page', 'integer:1..100')
        ];
    }

    // Main function. Process request and return ApiResponse.
    public function __invoke(Request $request, Response $response, callable $next)
    {
        // ... do some business logic as filtering, database requests etc.
        
        // All validated values are acessible via $this->values array
        $id = $this->values['id'];
        if ($id && isset($this->values['page'])) {
            ...
        }
        
        return new JsonApiResponse(200, ['data' => [
            'id' => 1,
            'name' => 'Samuel'
        ]]);
    }
}
```

## Validations

**IMPORTANT!**
Every parameter is parsed as string! So use **numeric** validation rule instead of integer or float.

Primary validation is handled by [Nette Validators](https://doc.nette.org/cs/2.4/validators).
Validations are registered in handlers `validate()` function. All validated input parameters are accessible in handler via `$this->values` array.

If any validation failed, api automatically send response with 400 HTTP code with all validation errors.

### Custom validation rules

By default you don't need to register new Validator instance to Api. But if you want register new validations or override existing validations you need to create and configure your own Validator instance.

```php
$validator = new Kelemen\ApiNette\Validator\Validator();
$validator->setValidator('enum', function ($value, $ruleParams = null) {
    // $value - contains parameter value
    // $ruleParams - contains string from parsed rule after ":"
    return in_array($value, explode(',', $ruleParams));
});

// Usage in validation funciton ...

public function validate()
{
    return new Validation('get', 'name', 'required|enum:Samuel,Peter')
}

```

### Inputs

Validator has defined set of default inputs

| Keyword       | Description   |
| ------------- |-------------|
| get           | $_GET |
| post          | $_POST |
| cookie        | $_COOKIES |
| file          | $_FILES |
| postRaw       | file_get_contents("php://input") |
| json          | json_decode(file_get_contents("php://input"), true) |
| path          | Parsed params from matched route |

If you want some special input you can add this input to Validator with `setInput($name, InputInterface $input)` function. 

## Middleware

Api flow can be extended by middleware. Middleware interface has only one function `__invoke(Request $request, Response $response, callable $next)`.
How middleware works: 

```php
use Kelemen\ApiNette\Middleware\Middleware;
use Nette\Http\Request;
use Nette\Http\Response;

class CustomMiddleware implements Middleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        // This code is executed before handler (Optional)
        // Example: provide authentification here. If user is authenticated call $next() if no return new response
        if ($request->isSecured()) {
            // Do something ...
        }

        // Call next middleware or handler if last middleware (Optional)
        $resp = $next($request, $response);

        // This code is executed after handler (Optional)
        // Example: add CORS headers here
        $response->setHeader('custom header', 'header value');

        // Mandatory! Every middleware has to return response!
        return $resp;
    }

}
```

If we have 3 middlewares registered as 
```php
['middleware1', 'middleware2', 'middleware3']
```

Flow will look like:
```
- middleware1
    - middleware2
        - middleware3
            - handler (return Nette\Application\IResponse)
        - middleware3
    - middleware2
- middleware1
```

## Flow and Exceptions

Library is shipped with default api presenter `Kelemen\ApiNette\Presenter\ApiPresenter`. This presenter running api and handle all exceptions (create response depends on catched exception).
If you want custom error responses, create and register your own presenter.

**Api throws this exceptions**

| Exception       | Description   |
| ------------- |-------------|
| Kelemen\ApiNette\Exception\ApiNetteException | Base parent exception |
| Kelemen\ApiNette\Exception\UnresolvedHandlerException | Handler registered for resolved route doesn't exists in container |
| Kelemen\ApiNette\Exception\UnresolvedMiddlewareException | Middleware registered for resolved route doesn't exists in container |
| Kelemen\ApiNette\Exception\UnresolvedRouteException | None of the registered routes match given url |
| Kelemen\ApiNette\Exception\ValidationFailedException | Some of registered validations failed |
| Kelemen\ApiNette\Exception\ValidatorException | Input type use in validation is not registered in validator |

## Base Implementations

### Middleware
#### CORSMiddleware
Setup Access-Control-Allow-Origin and Access-Control-Allow-Credentials headers. Middleware has 3 modes:
- all - returns allow-origin as "*". Credentials header disabled by standard.
- mirror - returns request "Origin" header in allow-origin and credentials header can be configured.
- custom - allow-origin and credentials header has to be configured.

### Handler
#### OptionsPreflightHandler
