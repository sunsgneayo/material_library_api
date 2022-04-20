<?php

declare(strict_types=1);
namespace App\Service\Video;


use App\Model\Video;
use App\Service\AbstractService;

class VideoService extends AbstractService
{
    /**
     * @param int $page 页码
     * @param int $size 页大小
     * @param array $where 查询条件
     * @return array
     */
    public function getVideoList(int $page , int $size ,array $where): array
    {
        $vCount = Video::query()->where($where)->count();
        $vList  = Video::query()->with("VideoCategory")->offset( ($page - 1) * $size )->limit($size)->where($where)
                ->select(["id","cid","title","describe","url","cover","clicks","shares","status","sort","comments","collects"])
                ->orderBy("sort")
                ->get();
        return [
            "data" => $vList->isNotEmpty() ? $vList->toArray() : [],
            "total" => $vCount
        ];
    }

    /**
     * @param array $inputData 写入组
     * @return bool
     */
    public function setVideoInfo(array $inputData = []) :bool
    {
        $saveData = [];
        if (isset($inputData["id"])  && $inputData["id"])
        {
            $saveData["id"] = $inputData["id"];
        }
        if (isset($inputData["title"]) && $inputData["title"])
        {
            $saveData["title"] = $inputData['title'];
        }
        if (isset($inputData["status"]) && $inputData["status"] || $inputData["status"] <= 0)
        {
            $saveData["status"] = $inputData['status'];
        }
        if (isset($inputData["sort"]) && $inputData["sort"])
        {
            $saveData["sort"] = $inputData['sort'];
        }
        if (isset($inputData["cover"]) && $inputData["cover"])
        {
            $saveData["cover"] = $inputData['cover'];
        }
        if (isset($inputData["url"]) && $inputData["url"])
        {
            $saveData["url"] = $inputData['url'];
        }
        if (isset($inputData["shares"]) && $inputData["shares"])
        {
            $saveData["shares"] = $inputData['shares'];
        }
        if (isset($inputData["cid"]) && $inputData["cid"])
        {
            $saveData["cid"] = $inputData['cid'];
        }
        if (isset($inputData["clicks"]) && $inputData["clicks"])
        {
            $saveData["clicks"] = $inputData['clicks'];
        }
        if (isset($inputData["collects"]) && $inputData["collects"])
        {
            $saveData["collects"] = $inputData['collects'];
        }
        if (isset($inputData["comments"]) && $inputData["comments"])
        {
            $saveData["comments"] = $inputData['comments'];
        }
        if (isset($inputData["describe"]) && $inputData["describe"])
        {
            $saveData["describe"] = $inputData['describe'];
        }

        $video = new Video();
        $res = $video->saveInfo($saveData);
        if ($res){
            return true;
        }else
        {
            return false;
        }
    }

    /**
     * @param int $vid 视频ID
     * @return bool
     */
    public function delVideoInfo(int $vid):bool
    {
        $video = new Video();

        $res = $video->deleteInfo($vid);
        if ($res)
        {
            return true;
        }else{
            return false;
        }
    }


    public function getVideoCommentList(int $vid):array
    {

    }

    public function getVideoCollectList(int $vid):array
    {

    }
}