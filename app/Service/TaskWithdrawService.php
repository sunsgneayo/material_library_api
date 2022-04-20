<?php


namespace App\Service;


use App\Model\TaskMember;
use App\Model\TaskWithdraw;
use Hyperf\DbConnection\Db;

class TaskWithdrawService extends AbstractService
{


    /**
     * @param array $where
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getTaskWithdrawList(array $where ,int $page,int $size):array
    {

        $query = TaskWithdraw::query()->with('member')->offset(($page - 1) * $size)->limit($size)->orderByDesc('id')->where($where)->get();

        $total = TaskWithdraw::query()->where($where)->count();


        return $query->isNotEmpty() ? [
            'total' => $total,
            'data'  => $query->toArray()
        ] : [];

    }

    /**
     * @param int $id
     * @param int $status
     * @return bool
     */
    public function examineTaskWithdraw(int $id,int $status) :bool
    {
        Db::beginTransaction();
        try{

            $withdraw = TaskWithdraw::query()->where('id','=',$id)->first();

            /**
             * 状态重复
             */
            if ($withdraw->status == $status)
            {
                return false;
            }
            if ($withdraw)
            {
                $withdraw->setAttribute('status',$status);
                /**
                 * 未通过提现审核
                 */
                if ($status == 2)
                {
                    $member = TaskMember::query()->where('id','=',$withdraw->member_id)->first();
                    $member->setAttribute('money',$member->money + $withdraw->money);
                    $member->save();

                }
                if ($status == 1)
                {
                    $withdraw->setAttribute('member_money',$withdraw->member_money - $withdraw->money);
                }

                $withdraw->save();
                Db::commit();
                return true;
            }
            return false;
        } catch(\Throwable $ex){
            Db::rollBack();
            return false;
        }

    }
}