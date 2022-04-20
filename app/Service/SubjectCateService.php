<?php


namespace App\Service;


use App\Model\Subject;
use App\Model\SubjectsCategory;
use Exception;

class SubjectCateService extends AbstractService
{
    public function getList(int $page,int $size): array
    {
        $cate = SubjectsCategory::query()->offset(($page-1) * $size )->limit($size)->orderBy("sort", 'DESC')->get();

        $count = SubjectsCategory::query()->count();


        return [
            "data" => $cate->isNotEmpty() ? $cate->toArray() : [],
            "total" => $count
        ];
    }

    public function setInfo(array $input = []) :bool
    {

        $saveData = [];
        if (isset($input["id"]) && $input["id"])
        {
            $saveData["id"]  =  $input['id'];
        }
        if (isset($input["name"]) && $input["name"])
        {
            $saveData["name"]  =  $input['name'];
        }
        if (isset($input["sort"]) && $input["sort"])
        {
            $saveData["sort"]  =  $input['sort'];
        }
        if (isset($input["status"]) && $input["status"] || $input['status'] < 0)
        {
            $saveData["status"]  =  $input['status'];
        }

        $cate = new SubjectsCategory();

        $res = $cate->saveInfo($saveData);

        if ($res) {
            return true;
        }
        return  false;
    }

    public function delInfo(int $id)
    {
        $data = SubjectsCategory::query()->where([
            ['id', '=', $id]
        ])->first();

        if ($data) {
            try {
                return $data->delete();
            } catch (Exception $e) {
                return false;
            }
        }

        return false;

    }

    /**
     * 检测分类视频存在
     * @param int $cid 视频分类ID
     */
    public function isExistenceSubject(int $cid): bool
    {
        $v = Subject::query()->where("category_id",$cid)->get();

        return $v->isNotEmpty();
    }

}