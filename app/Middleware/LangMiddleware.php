<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LangMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //获取客户端语言代码
        $lang = $request->getHeaderLine('Accept-Language') ?? '';

        $request = Context::get(ServerRequestInterface::class);

        switch (trim($lang)) {
            case 'en':
                $request = $request->withAttribute('lang',0);
                break;
            case 'zh':
                $request = $request->withAttribute('lang', 1);
                break;
            case 'th':
                $request = $request->withAttribute('lang', 2);
                break;
            case 'vi':
                $request = $request->withAttribute('lang', 3);
                break;
            default:
                $request = $request->withAttribute('lang', 1);
        }

        Context::set(ServerRequestInterface::class, $request);

        return $handler->handle($request);
    }
}