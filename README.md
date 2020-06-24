install
./artisan vendor:publish --provider="IliaKologrivov\LaravelJsonRpcServer\ServiceProvider\JsonRpcServerServiceProvider"

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
