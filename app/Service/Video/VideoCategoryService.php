<?php


declare(strict_types=1);
namespace App\Service\Video;


use App\Model\Video;
use App\Model\VideoCategory;
use App\Service\AbstractService;

class VideoCategoryService extends AbstractService
{

    public function getList(int $page,int $size):array
    {

        $vcList = VideoCategory::query()->offset( ($page -1) * $size )->limit($size)
            ->select("id","name","status","sort")
            ->orderBy("sort")
            ->get();
        $vcCount = VideoCategory::query()->count();

        return [
            "total" => $vcCount,
            "data"  => $vcList->isNotEmpty() ? $vcList->toArray() : []
        ];
    }
    public function setInfo(array $inputData = []): bool
    {
        $saveData = [];
        if (isset($inputData["name"]) && $inputData["name"])
        {
            $saveData["name"] = $inputData["name"];
        }
        if (isset($inputData["status"]) && $inputData["status"])
        {
            $saveData["status"] = $inputData["status"];
        }
        if (isset($inputData["sort"]) && $inputData["sort"])
        {
            $saveData["sort"] = $inputData["sort"];
        }
        if (isset($inputData["id"]) && $inputData["id"])
        {
            $saveData["id"] = $inputData["id"];
        }
        $vc = new VideoCategory();
        $result = $vc->saveInfo($saveData);
        if ($result){
            return true;
        }
        return false;

    }
    public function delInfo(int $cid): bool
    {
        $video = new VideoCategory();
        $res = $video->deleteInfo($cid);
        if ($res)
        {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 检测分类视频存在
     * @param int $cid 视频分类ID
     */
    public function isExistenceVideo(int $cid): bool
    {
        $v = Video::query()->where("cid",$cid)->get();

        return $v->isNotEmpty();
    }
}