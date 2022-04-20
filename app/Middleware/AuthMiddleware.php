<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponseInterface;
use Phper666\JWTAuth\JWT;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    protected $prefix = 'Bearer';
    /**
     * @var array
     */
    protected $ignore = [
        '/api/Common/login',
        '/api/Common/agentLogin',


        '/api/getContentList',  // 文字内容
        '/api/getImageList',   // 图片内容
        '/api/getContentLists',
        '/api/getImageLists',  //


        /**
         * 代理app-素材生成
         */
        '/api/getAllImageList',  //获取单个类型下的所有图片
        '/api/getAllContentList',  //获取单个类型下的所有文字内容
        '/api/getAllImageByTypes',  //获取多个类型下的所有文字内容

        /**
         * 代理后台
         */
        "/api/agent/getVideoList", //代理后台短视频列表(分享)
        "/api/agent/getMemberPrizesList", //参与用户抽奖记录
        "/api/agent/getSubjectCateList", //题库列表
        "/api/agent/getSubjectList", //题目列表


        /**
         * 中转页
         */
        '/api/transfer/getTransferImagesInfo',
        '/api/transfer/getTransferContentInfo',
        '/api/channelimage/getInfo',

        /**
         * 答题注册--抽奖
         */
        '/api/getSubjectsList',
        '/api/getPrizeProbability',
        '/api/getPrizesList',
        '/api/setSubjects',  //答题
        '/api/getMemberPrizeLog', //获取用户抽奖记录
        '/api/getMemberSubjects', //获取用户答题记录
        '/api/getRandomSubList' //随机取10
    ];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var JWT
     */
    protected $jwt;

    /**
     * @Inject()
     * @var HttpResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getUri()->getPath(), $this->ignore)) {
            return $handler->handle($request);
        }

        $token = $request->getHeaderLine('Authorization') ?? '';

        if (strlen($token) > 0) {
            $token = explode($this->prefix . ' ', ucfirst($token))[1] ?? '';
            try {
                if (strlen($token) > 0 && $this->jwt->checkToken()) {
                    $data = $this->jwt->getParserData();


                    $request = Context::get(ServerRequestInterface::class);
                    $request = $request->withAttribute('user', $data);

                    Context::set(ServerRequestInterface::class, $request);

                    return $handler->handle($request);
                }
            } catch (InvalidArgumentException | Throwable $e) {
                return $this->response->json([
                    'status' => 401,
                    'msg'    => $e->getMessage()
                ]);
            }
        }

        return $this->response->json([
            'status' => 401,
            'msg'    => 'Token验证失败'
        ]);
    }
}