## Installation

```bash
composer require iliakologrivov/laravel-json-rpc-server
./artisan vendor:publish --provider="IliaKologrivov\LaravelJsonRpcServer\ServiceProvider\JsonRpcServerServiceProvider"
```

Добавить загрузку опубликованного провайдера App\Providers\JsonRpcRouteServiceProvider в config/app.php. 
Настроить роуты в App\Providers\JsonRpcRouteServiceProvider где json_rpc.php название файла с роутами для json-rpc сервера.
Обьявление роутов происходит через фасад JsonRpcRoute в любом месте или через фасад и $route в файле роутов json-rpc.

```php
/**
 * @var IliaKologrivov\LaravelJsonRpcServer\Server\Router $router
 */
use IliaKologrivov\LaravelJsonRpcServer\Facades\JsonRpcRoute;
use IliaKologrivov\LaravelJsonRpcServer\Server\Router;
use App\Http\Controllers\Controller;
JsonRpcRoute::method('foo', 'Controller@bar');

JsonRpcRoute::endPoint('/users', function(Router $router) {
    JsonRpcRoute::method('create', ['uses' => 'UserController@create']);

    JsonRpcRoute::group(['endpoint' => '/v1', 'middleware' => [TestMiddleware::class]], function() {
        JsonRpcRoute::method('show', ['uses' => 'UserController@show', 'middleware' => [TestMethodMiddleware::class]]);
    });

    JsonRpcRoute::group(['endpoint' => '/v2', 'namespace' => '\\App\\JsonRpcV2\\Controllers'], function(Router $router) {
        $router->method('insert', ['uses' => 'UserController@insert']);

        JsonRpcRoute::group(['endpoint' => '/level2'], function(Router $router) {
            $router->method('update', ['uses' => 'UserController@update']);
        });
    });

    JsonRpcRoute::method('update', [Controller::class, 'update']);

    JsonRpcRoute::method('delete', function(Request $request) {
        return [
            'success' => true,
        ];
    });
});
```

```bash
./artisan json-rpc-route:list
+--------------------------+--------+-------------------------------------------------+----------------------------------------+
| Endpoint                 | Method | Controller                                      | Middleware                             |
+--------------------------+--------+-------------------------------------------------+----------------------------------------+
| json_rpc                 | foo    | App\Http\Controllers\Controller@bar             | []                                     |
| json_rpc/users           | create | App\Http\Controllers\UserController@create      | []                                     |
| json_rpc/users           | update | App\Http\Controllers\Controller@update          | []                                     |
| json_rpc/users           | delete | Closure                                         | []                                     |
| json_rpc/users/v1        | show   | App\Http\Controllers\UserController@show        | [TestMiddleware, TestMethodMiddleware] |
| json_rpc/users/v2        | insert | App\JsonRpcV2\Controllers\UserController@insert | []                                     |
| json_rpc/users/v2/level2 | update | App\JsonRpcV2\Controllers\UserController@update | []                                     |
+--------------------------+--------+-------------------------------------------------+----------------------------------------+
```

Контроллеры - все так же как и в Laravel, но 
Request - IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface
FormRequest - IliaKologrivov\LaravelJsonRpcServer\Server\FormRequest

```php
namespace App\JsonRpc\Requests;

use IliaKologrivov\LaravelJsonRpcServer\Server\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:5',
        ];
    }
}

namespace App\Http\Controllers;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface as Request;
use App\JsonRpc\Requests\StoreRequest;

class ControllerV2 extends Controller
{
    public function bar(Request $request)
    {
        return $request->all();
    }
    
    public function store(StoreRequest $request)
    {
        return $request->all();
    }

}
```
