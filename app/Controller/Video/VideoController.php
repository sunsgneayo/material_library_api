<?php

declare(strict_types=1);

namespace App\Controller\Video;

use App\Controller\Http\AbstractController;

use App\Service\Video\VideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController (prefix="api/video")
 * Class VideoController
 * @package App\Controller\Video
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
        $page = $this->request->input("page",$this->page);
        $size = $this->request->input("size",$this->size);
        $cid  = $this->request->input("cid");
        $title  = $this->request->input("title");

        $where = [];
        if ($cid)
        {
            $where[] = ["cid","=",$cid];
        }
        if ($title)
        {
            $where[] = ['title',"LIKE","%$title%"];
        }
        $data = $this->videoService->getVideoList(intval($page),intval($size),$where);
        return $this->jsonResponse(200,"",$data);
    }

    /**
     * @PostMapping (path="setVideoInfo")
     * @return ResponseInterface
     */
    public function setVideoInfo(): ResponseInterface
    {
        $inputData = $this->request->all();
        $result = $this->videoService->setVideoInfo($inputData);
        if ($result)
        {
            return $this->jsonResponse(200,"success");
        }
        return  $this->jsonResponse(202,'error');
    }

    /**
     *
     * @PostMapping (path="delVideoInfo")
     */
    public function delVideoInfo(): ResponseInterface
    {
        $id = $this->request->input("id");
        if ($id)
        {
            $result = $this->videoService->delVideoInfo(intval($id));
            if ($result)
            {
                return $this->jsonResponse(200,'',[]);
            }
            return  $this->jsonResponse(201,'');
        }
        return $this->jsonResponse(202,'');
    }
//    public function getVideoComment()
//    {
//
//
//        $vid = $this->request->input("vid");
//
//        if ($vid)
//        {
//            $data = $this->videoService->getVideoCommentList(intval($vid));
//        }
//    }
}
