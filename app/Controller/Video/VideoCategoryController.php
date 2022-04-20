<?php

declare(strict_types=1);

namespace App\Controller\Video;
use App\Service\Video\VideoCategoryService;
use App\Service\Video\VideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;
use App\Controller\Http\AbstractController;

/**
 * @AutoController (prefix="api/video_category")
 * Class VideoCategoryController
 * @package App\Controller\Video
 */
class VideoCategoryController extends AbstractController
{
    /**
     * @Inject
     * @var VideoCategoryService
     */

    protected $videoCategoryService;

    /**
     * @GetMapping (path="getVideoCategoryList")
     */
    public function getVideoCategoryList(): ResponseInterface
    {

        $page = $this->request->input("page",$this->page);
        $size = $this->request->input("size",$this->size);

        $data = $this->videoCategoryService->getList(intval($page),intval($size));

        return $this->jsonResponse(200,'',$data);
    }


    /**
     * @PostMapping (path="setVideoCategoryInfo")
     */
    public function setVideoCategoryInfo(): ResponseInterface
    {

        $inputData = $this->request->all();

        $result = $this->videoCategoryService->setInfo($inputData);
        if ($result)
        {
            return $this->jsonResponse(200,"",[]);
        }
        return $this->jsonResponse(202);

    }

    /**
     * @PostMapping (path="delVideoCategoryInfo")
     */
    public function delVideoCategoryInfo(): ResponseInterface
    {

        $id = $this->request->input("id");
        if (!$id)
        {
            return $this->jsonResponse(400);
        }
        $isExistence = $this->videoCategoryService->isExistenceVideo(intval($id));
        if (!$isExistence)
        {
            $result = $this->videoCategoryService->delInfo(intval($id));
            if ($result)
            {
                return $this->jsonResponse(200,"删除成功");
            }

            return $this->jsonResponse(201);
        }

        return $this->jsonResponse(201,"该分类下存在视频");

    }
}
