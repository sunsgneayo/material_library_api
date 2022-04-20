<?php

declare(strict_types=1);

namespace App\Controller\Http;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    public $page = 1;

    public $size = 10;
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
     * @var int
     */
    protected  $admin_id;


    public function __construct()
    {
        $this->admin_id = $this->request->getAttribute('user')['id'];
    }

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
                $key = 1;
        }
        return $key;
    }
}
