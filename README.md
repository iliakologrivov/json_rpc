Добавить HandlerException в AppServiceProvider где App\Exceptions\JsonRpcHandler ваш путь до HandlerException
```php
$this->app->singleton(IliaKologrivov\LaravelJsonRpcServer\Exception\Handler::class, App\Exceptions\JsonRpcHandler::class);
```
Добавить пустой App\Exceptions\JsonRpcHandler
```php
namespace App\Exceptions;

use IliaKologrivov\LaravelJsonRpcServer\Exception\Handler;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface;
use IliaKologrivov\LaravelJsonRpcServer\Server\Response;

class JsonRpcHandler extends Handler
{
    protected $dontReport = [

    ];

    public function report(\Throwable $exception): void
    {
        parent::report($exception);
    }

    public function render(\Throwable $exception, ?RequestInterface $request = null): Response
    {
       return parent::render($exception, $request);
    }
}
```

Добавить роуты в App\Providers\RouteServiceProvider где json_rpc.php название файла с роутами для json-rpc сервера
```php
\IliaKologrivov\LaravelJsonRpcServer\Facades\JsonRpcRoute::group(['namespace' => $this->namespace], base_path('routes/json_rpc.php'));
```

Обьявление роутов происходит через фасад JsonRpcRoute в любом месте или через фасад и $route в файле роутов json-rpc
```php
/**
 * @var IliaKologrivov\LaravelJsonRpcServer\Server\Router $router
 */
use IliaKologrivov\LaravelJsonRpcServer\Facades\JsonRpcRoute;
use IliaKologrivov\LaravelJsonRpcServer\Server\Router;
use App\Http\Controllers\Controller;
JsonRpcRoute::endPoint('/api', function(Router $router) {});//установка точки для обращения по http к json-rpc, внутри callback функции есть $route с классом роутера
JsonRpcRoute::method('foo', 'Controller@bar');//назначение метода json-rpc на метод контроллера Controller@bar
//example using
JsonRpcRoute::endPoint('/api', function(Router $router) {
    JsonRpcRoute::method('foo', ['uses' => 'Controller@bar']);
    JsonRpcRoute::method('foo', [Controller::class, 'bar']);
    JsonRpcRoute::method('foo', ['App\Http\Controllers\Controller', 'bar']);
    JsonRpcRoute::method('foo', ['uses' => [Controller::class, 'bar']]);
    JsonRpcRoute::method('foo', ['uses' => ['App\Http\Controllers\Controller', 'bar']]);
    $router->method('foo', 'Controller@bar');
    //как и в роутере Laravel 'namespace' в методе group переназначает namespace полность, endpoint при этом конкатенирует запись
    JsonRpcRoute::group(['namespace' => '\\v2', 'endpoint' => 'v2'], function() {
        //endpoint - http://example.com/api/v2, controller - \v2\ControllerV2@bar 
        JsonRpcRoute::method('foo', ['uses' => 'ControllerV2@bar']);
    });

});
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
