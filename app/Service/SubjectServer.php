<?php


namespace App\Service;



use App\Exception\ApiException;
use App\Exception\InputException;
use App\Model\PrizesMember;
use App\Model\Subject;
use App\Model\SubjectsMember;
use Exception;
use Hyperf\Utils\Codec\Json;

class SubjectServer extends AbstractService
{


    /**
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getSubjectsList(int $page,int $size,array $where):array
    {
        $subject = Subject::query()->with(["adminUser" , "category"])
            ->where($where)
            ->offset(($page-1) * $size )->limit($size)
            ->orderBy("sort", 'DESC')->get();

        $total  = Subject::query()->where($where)->count();
        return
            [
                "total" => $total,
                "data"  => $subject->isNotEmpty() ? $subject->toArray() : []
            ];
    }

    /**
     * 随机取limit
     * @param int $limit
     * @param array $where
     * @return array
     */
    public function getRandomSubjectList(int  $limit , array $where = []): array
    {
        $list = Subject::query()->with(['category'])->where($where)->limit($limit)->get();


        return $list->isNotEmpty() ? $list->toArray() : [];
    }
    public function saveSubject(int $admin_id,array $inputData = []):bool
    {
        $saveData = [];
        $saveData["admin_id"] = $admin_id;
        if (isset($inputData['id']) && $inputData['id']) {
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['subject']) && $inputData['subject']) {
            $saveData['subject'] = $inputData['subject'];
        }
        if (isset($inputData['category_id']) && $inputData['category_id']) {
            $saveData['category_id'] = $inputData['category_id'];
        }
        if (isset($inputData['options']) && $inputData['options']) {
            $saveData['options'] = $inputData['options'];
        }
        if (isset($inputData['answer']) && $inputData['answer'] || $inputData['answer'] <= 0) {
            $saveData['answer'] = $inputData['answer'];
        }
        if (isset($inputData['status']) && $inputData['status'] || $inputData['status'] < 0) {
            $saveData['status'] = $inputData['status'];
        }
        if (isset($inputData['sort']) && $inputData['sort']) {
            $saveData['sort'] = $inputData['sort'];
        }
        $sub = new Subject();
        $id = $sub->saveInfo($saveData);
        if ($id)
        {
            return true;
        }
        return  false;

    }

    /**
     * @param int $admin_id
     * @param array $inputData
     * @return bool
     */
    public function batchSaveSubjects( int $admin_id,array  $inputData = []): bool
    {
        $saveData = [];
        for ($i = 0; $i < count($inputData); $i++) {
            $saveData[$i]["admin_id"] = $admin_id;
            if (isset($inputData[$i]['id']) && $inputData[$i]['id']) {
                $saveData[$i]['id'] = $inputData[$i]['id'];
            }
            if (isset($inputData[$i]['subject']) && $inputData[$i]['subject']) {
                $saveData[$i]['subject'] = $inputData[$i]['subject'];
            }
            if (isset($inputData[$i]['options']) && $inputData[$i]['options']) {
                $saveData[$i]['options'] = $inputData[$i]['options'];
            }
            if (isset($inputData[$i]['answer']) && $inputData[$i]['answer']) {
                $saveData[$i]['answer'] = $inputData[$i]['answer'];
            }
            if (isset($inputData[$i]['status']) && $inputData[$i]['status']) {
                $saveData[$i]['status'] = $inputData[$i]['status'];
            }
            if (isset($inputData[$i]['sort']) && $inputData[$i]['sort']) {
                $saveData[$i]['sort'] = $inputData[$i]['sort'];
            }

            $sub = new Subject();
            $id =  $sub->saveInfo($saveData[$i]);
            if (!$id)
            {
                return  false;
            }
        }
        return true;
    }

    public function delSubject(int $id) : bool
    {
        $data = Subject::query()->where([
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
     * @param int $uid
     * @param int $cid
     * @param array $option
     * @return array
     */
    public function submitSubjectResult(int $uid , int $cid, array $option): array
    {
        $subjectMember = SubjectsMember::query()->where([
            ["uid" , "=" ,$uid],
            ["cid" , "=" ,$cid],
        ])->get();

        if ($subjectMember -> isNotEmpty())
        {
            throw new ApiException(302,"Repeat the answer");
        }
        try {
            $memberResult = new SubjectsMember();
            $memberResult->setAttribute("uid",$uid);
            $memberResult->setAttribute("cid",$cid);
            $memberResult->setAttribute("option",$option);
            $memberResult->setAttribute("click",0);
            $memberResult->save();
        }catch (Exception $exception)
        {
            throw new ApiException(202,"Answer the questions on failure");
        }
        return [];

    }

    /**获取答题记录,并计算正确次数
     * @param int $uid
     * @param int $cid
     * @return array
     */
    public function getMemberSubjectsInfo(int $uid,int $cid): array
    {
        $log = SubjectsMember::query()->where([
            ["uid" ,"=" , $uid],
            ["cid" ,"=" , $cid],
        ])->select('option',"id","click")->first();
        if (!$log)
        {
            return [
                "log" => null,
                "answer" => []
            ];
        }
        $option = $log->getAttributeValue("option");
        $answer = Subject::query()->where([
            ["category_id","=",$cid],
            ["status","=",1]
        ])->select("id","answer")->get();
        //未设置正确答案
        foreach ($answer as $v)
        {
            if (!$v->answer)
            {
                return [
                    "log" => [
                        "option" => $option
                    ],
                    "answer" => null
                ];
            }
        }
        //已抽奖
        $memberPrize = PrizesMember::query()->where([
            ["uid","=",$uid],
            ["cid","=",$cid],
        ])->count();
        if ($memberPrize)
        {
            return [
                "log" => [
                    "option" => $option
                ],
                "answer" =>  $answer->isNotEmpty() ? $answer->toArray() : [] ,
                "click" => $log->getAttributeValue("click")
            ];
        }

        //计算答对次数
        $answerNum =  0;

        foreach ($answer as $k => $v)
        {
            if ($option[$v->id] == $v->answer)
            {
                $answerNum++;
            }
        }
        $log->setAttribute("click",$answerNum);
        $log->save();

        return [
            "log" => [
                "option" => $option
            ],
            "answer" =>  $answer->isNotEmpty() ? $answer->toArray() : [] ,
            "click" => $answerNum
        ];
    }
}