<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Task;
use App\Model\TaskMember;
use App\Model\TaskTodo;
use Hyperf\DbConnection\Db;

use Hyperf\Database\Model\Builder;
use PhpParser\Node\Stmt\Foreach_;

class TaskExamineService extends AbstractService
{
    /**
     * @param int $page
     * @param int $size
     * @param array $map
     * @param string $task_title
     * @return array
     */
    public function getTaskExamineList(int $page, int $size, array $map,string $task_title = ''): array
    {
        $total = TaskTodo::query()->where($map)->whereHas('task',function ($task)use ($task_title){
            if ($task_title != '')
            {
                $task->whereRaw("JSON_SEARCH(`title`, 'one', '%". $task_title."%')");
            }
        })->count();

        $data = TaskTodo::query()->with(['task','member'])
            ->whereHas('task',function ($task)use ($task_title){
                if ($task_title != '')
                {
                    $task->whereRaw("JSON_SEARCH(`title`, 'one', '%". $task_title."%')");
                }
            })
            ->where($map)->offset(($page - 1) * $size)->limit($size)
            ->orderBy('received_at', 'DESC')
            ->orderBy('status', 'ASC')
            ->select(['id', 'task_id', 'member_id', 'quantity','received_at', 'submited_at', 'examined_at', 'status', 'reason','annex'])
            ->get();
        return $data->isNotEmpty() ? [
            'total' => $total,
            'data'  => $data->toArray(),
        ] : [];
    }

    /**
     * @param int $id
     * @param int $status
     * @param string $reason
     * @param int $quantity
     * @return false
     */
    public function examineTask(int $id, int $status, string $reason = '',int $quantity):bool
    {
        $data = TaskTodo::query()->with('task')->where([
            ['id', '=', $id]
        ])->first();
        /**
         * 状态重复
         */
        if ($data->status == $status)
        {
            return false;
        }

        /**
         * 开启事务
         */
        Db::beginTransaction();
        try{

            if ($data) {
                $data->setAttribute('status', $status);

                $member= TaskMember::query()->where('id','=',$data->member_id)->first();
                if ($quantity)
                {
                    $data->setAttribute('quantity', $quantity);
                }
                if ($reason)
                {
                    $data->setAttribute('reason', $reason);
                }
                $data->setAttribute('examined_at', date('Y-m-d H:i:s'));
                //审核通过
                if ($status == 3)
                {
                    if ($quantity && $data->task->type == 2)
                    {
                        $member->setAttribute('money',$member->money + ($quantity * $data->task->price));
                    }else{
                        $member->setAttribute('money',$member->money + $data->task->price);
                    }


                    $member->save();
                }
                $data->save();
                Db::commit();
                return true;
            } else {
                return false;
            }

        } catch(\Throwable $ex){
            Db::rollBack();
            return false;
        }

    }
}