<?php


namespace App\Service;


use App\Exception\ApiException;
use App\Model\Prize;
use App\Model\PrizesMember;
use App\Model\SubjectsMember;
use Exception;
use Hyperf\DbConnection\Db;
class PrizesService extends AbstractService
{

    /**
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getList(int $page,int $size): array
    {
        $count = Prize::query()->count();
        $prize = Prize::query()->offset(($page-1) * $size )->limit($size)->orderBy("sort")->get();
        return ["count" => $count , "data" => $prize->isNotEmpty() ? $prize->toArray() : []];
    }

    /**
     * @param array $inputData
     * @return bool
     */
    public function saveInfo(array $inputData = []) : bool
    {

        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']) {
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['name']) && $inputData['name']) {
            $saveData['name'] = $inputData['name'];
        }
        if (isset($inputData['probability'])  || $inputData['probability'] == 0) {
            $saveData['probability'] = $inputData['probability'];
        }
        if (isset($inputData['status']) && $inputData['status'] || $inputData['status'] <= 0) {
            $saveData['status'] = $inputData['status'];
        }
        if (isset($inputData['image']) && $inputData['image']) {
            $saveData['image'] = $inputData['image'];
        }
        if (isset($inputData['sort']) && $inputData['sort']) {
            $saveData['sort'] = $inputData['sort'];
        }
        if (isset($inputData['type']) && $inputData['type']) {
            $saveData['type'] = $inputData['type'];
        }
        $prize = new Prize();

        $result = $prize->saveInfo($saveData);
        if ($result)
        {
            return true;
        }
        return  false;
    }

    /**
     * 验证概率是否有效
     */
    /**
     * @param array $inputData
     */
    public function verificationProbability(array $inputData = []): bool
    {

        $prizes = Prize::query()->where("status" ,"=" , 1)->get();
        $oldCount = 0;
        if (isset($inputData["id"]) && $inputData["id"])
        {
            $prize  =  Prize::find($inputData["id"]);
            if ($prize)
            {
                //状态有效
                foreach ($prizes as $v)
                {
                    if ($v->id !== $inputData["id"])
                    {
                        $oldCount += $v->probability;
                    }
                }
                return !($oldCount + $inputData["probability"] > 100);
            }

        }

        //w
        foreach ($prizes as $v)
        {
            $oldCount += $v->probability;
        }

        return !($oldCount + $inputData["probability"] > 100);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delInfo(int $id):bool
    {

        $data = Prize::query()->where([
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
     * @param int $cid
     * @param int $uid
     * @param int $agent_id
     * @param string $username
     * @return array
     */
    public function getLuckydrawResult(int $cid , int $uid,int $agent_id,string $username = ""): array
    {
        $subject = SubjectsMember::query()->where("uid","=",$uid)->where("cid","=",$cid)->first();
        if (!$subject)
        {
            throw new ApiException(401,"No lucky draw");
        }
        $click =  $subject->getAttributeValue("click");
        if ($click <= 0)
        {
            throw new ApiException(402,"No lucky draw");
        }
        $Prizes = Prize::query()->where("status", "=", 1)->select("id", "name", "probability","type")->get();
        $arr = [];
        foreach ($Prizes as  $val) {
            $arr[$val->id] =  intval($val->probability  * 100);
        }
        $pid = $this->get_rand($arr);

        $Prize = [];
        foreach ($Prizes as $val) {
            if ($val->id == $pid)
            {
                $Prize = $val;
            }
        }
        //-----end------

        Db::beginTransaction();
        try {

            $subject->decrement("click",1);
            $subject->save();
            $prizes_member = new  PrizesMember();
            $prizes_member->setAttribute("pid",$Prize->id);
            $prizes_member->setAttribute("uid",$uid);
            $prizes_member->setAttribute("cid",$cid);
            $prizes_member->setAttribute("username",$username);
            $prizes_member->setAttribute("agent_id",$agent_id);
            $prizes_member->save();
            Db::commit();
        }catch (Exception $exception)
        {
            Db::rollBack();
            throw new ApiException(401,"error");
        }

        return [
            "prize" => $Prize->toArray(),
            "click" => $click - 1
        ];
    }

    /**
     * 概率取得中奖值
     * @param $proArr
     * @return int|string
     */
    private static function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }

    /**
     * 获取中奖记录
     * @param int $page
     * @param int $size
     * @param array $where
     * @return array
     */
    public function memberAwardRecords(int $page,int $size,array $where = []): array
    {

        $prizeMemberLog  = PrizesMember::query()->with(["prize","subjectCategory"])
            ->where($where)->select("id","pid","uid","cid","agent_id","username","created_at")
            ->offset(($page-1) * $size )->limit($size)
            ->get();
        $total = PrizesMember::query()->where($where)->count();

        return [
            "total" => $total,
            "data"  => $prizeMemberLog->isNotEmpty() ? $prizeMemberLog->toArray() : []
        ];
    }
}