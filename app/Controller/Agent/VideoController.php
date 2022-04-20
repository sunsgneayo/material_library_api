<?php

declare(strict_types=1);

namespace App\Controller\Agent;

use App\Controller\Home\AbstractController;
use App\Service\Video\VideoService;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

/**
 * 代理后台短视频分享
 * Class VideoController
 * @package App\Controller\Agent
 * @AutoController (prefix="api/agent")
 */
class VideoController extends AbstractController
{

    /**
     * @Inject ()
     * @var VideoService
     */
    protected $videoService;


    /**
     * @GetMapping (path="getVideoList")
     * @return ResponseInterface
     */
    public function getVideoList(): ResponseInterface
    {

        $page = $this->request->input("page", $this->page);
        $size = $this->request->input("size", $this->size);

        $data = $this->videoService->getVideoList(intval($page), intval($size), []);

        return $this->jsonResponse(200, "", $data);
    }
}
