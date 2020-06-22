<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\JsonResponse;

class Response implements Jsonable, Arrayable
{
    private $id;

    private $result;

    private $isError = false;

    /**
     * @param  string|int|null  $id
     * @param  mixed  $result
     * @param  bool  $isError
     */
    public function __construct($id, $result, bool $isError = false)
    {
        $this->id = empty($id) ? null : (is_int($id) ? $id : (string) $id);

        $this->result = ($result instanceof JsonResponse && method_exists($result, 'getData')) ? $result->getData() : $result;

        $this->isError = $isError;
    }

    public function toJson($options = 0)
    {
        return json_encode((object)$this->toArray(), $options);
    }

    public function toArray()
    {
        $result = [
            'jsonrpc' => '2.0'
        ];

        if (!empty($this->id)) {
            $result['id'] = $this->id;
        }

        if ($this->isError) {
            $result['error'] = $this->result;
        } else {
            $result['result'] = $this->result;
        }

        return $result;
    }

}
