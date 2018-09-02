<?php

namespace App\Http\Controllers;

use App\Transformers\SimpleSerializer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * Class ApiController
 * TODO: Move this class to a package so it can be reusable in other service
 *
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    protected $fractal;

    protected $request;
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * ApiController constructor.
     */
    public function __construct(Manager $fractal, Request $request)
    {
        $this->fractal = $fractal;

        $this->fractal->setSerializer(new SimpleSerializer);

        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondEmpty()
    {
        return $this->respond(['data' => []]);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondCreated($message = 'Resource Created')
    {
        return $this->setStatusCode(201)
            ->respond([
                'message' => $message,
            ]);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondUpdated($message = 'Resource Updated')
    {
        return $this->setStatusCode(200)
            ->respond([
                'message' => $message,
            ]);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondDeleted($message = 'Resource Deleted')
    {
        return $this->setStatusCode(200)
            ->respond([
                'message' => $message,
            ]);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'errors' => $message,
        ]);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondMethodNotAllowed($message = 'Method Not Allowed')
    {
        return $this->setStatusCode(405)->respondWithError($message);
    }

    /**
     * @param $messages
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondFailValidation($messages)
    {
        return $this->setStatusCode(422)->respondWithError($messages);
    }

    protected function respondWithItem($item, $callback)
    {
        $resource = new Item($item, $callback);

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray(['data' => $rootScope->toArray()]);
    }

    protected function respondWithCollection($collection, $callback)
    {
        $resource = new Collection($collection, $callback);

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray(['data' => $rootScope->toArray()]);
    }

    protected function respondWithPagination($paginator, $callback)
    {
        $resource = new Collection($paginator->getCollection(), $callback);

        $queryParams = array_diff_key($this->request->query(), array_flip(['page']));
        $paginator->appends($queryParams);

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray([
            'data' => $rootScope->toArray(),
            'meta' => $this->fractal->getSerializer()->paginator(new IlluminatePaginatorAdapter($paginator))
        ]);
    }

    protected function respondWithArray(array $array, array $headers = [])
    {
        $mimeTypeRaw = $this->request->server('HTTP_ACCEPT', '*/*');

        // If its empty or has */* then default to JSON
        if ($mimeTypeRaw === '*/*') {
            $mimeType = 'application/json';
        } else {
            $mimeParts = (array) explode(',', $mimeTypeRaw);
            $mimeType = strtolower($mimeParts[0]);
        }

        switch ($mimeType) {
            case 'text/html':
            case 'application/json':
                $contentType = 'application/json';
                $content = json_encode($array);
                break;

            default:
                $contentType = 'application/json';
                $content = json_encode([
                    'error' => [
                        'code' => static::CODE_INVALID_MIME_TYPE,
                        'message' => sprintf('Content of type %s is not supported.', $mimeType),
                    ]
                ]);
        }

        $response = response($content, $this->statusCode, $headers);
        $response->header('Content-Type', $contentType);

        return $response;
    }
}
