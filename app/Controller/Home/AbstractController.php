<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Exception\ApiException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Codec\Json;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

abstract class AbstractController
{
    public $page = 1;

    public $size = 10;

    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;


    /**
     * @param int $status
     * @param string $msg
     * @param array $data
     * @return HttpResponseInterface
     */
    protected function jsonResponse(int $status = 200, string $msg = '', array $data = []): HttpResponseInterface
    {
        return $this->response->json([
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data
        ]);
    }

    /**
     *
     * @return int
     */
    protected function langKey(): int
    {
        $lang = $this->request->getHeaderLine("Accept-Language") ?? "";
        switch (trim($lang)) {
            case 'en':
                $key = 1;
                break;
            case 'zh_CN':
                $key = 2;
                break;
            case 'zh_TW':
                $key = 3;
                break;
            case 'th':
                $key = 4;
                break;
            case 'vi':
                $key = 5;
                break;
            default:
                $key = 4;
        }
        return $key;
    }

    /**
     * 参数解密
     */
    protected  function decode($value): array
    {
        if (is_null($value))
        {
            throw new ApiException(400,"Query error");
        }

        $str =  urldecode(base64_decode(urldecode($value)));

        return Json::decode($str);
    }
}
