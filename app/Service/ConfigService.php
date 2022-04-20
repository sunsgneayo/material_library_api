<?php


namespace App\Service;


use App\Model\TaskTodo;

class ConfigService extends AbstractService
{

    /**
     * @param int $task_id
     * @return array
     */
    public static function getTaskAllMoney(int $task_id): array
    {
        $data = TaskTodo::query()->with(['task'])

            ->where("task_id","=" ,$task_id)
            ->where('status','=',3)
            ->select(['id', 'task_id', 'member_id', 'quantity', 'submited_at', 'examined_at', 'status', 'reason','annex'])
            ->get();

        $money = intval(0);
        if ($data){
            foreach ($data as $k => $v)
            {
                if ($v->task->type == 2)
                {
                    $money += $v->task->price * $v->quantity;
                }
                $money += $v->task->price;
            }
        }
        return ["money" => $money];
    }


    public static function staticGetTaskAllMoney(int $task_id): int
    {
        $data = TaskTodo::query()->with(['task'])

            ->where("task_id","=" ,$task_id)
            ->where('status','=',3)
            ->select(['id', 'task_id', 'member_id', 'quantity','received_at', 'submited_at', 'examined_at', 'status', 'reason','annex'])
            ->get();

        $money = intval(0);
        if ($data){
            foreach ($data as $k => $v)
            {
                if ($v->task->type == 2)
                {
                    $money += $v->task->price * $v->quantity;
                }else{
                    $money += $v->task->price;
                }

            }
        }
        return $money;
    }
}