<?php


namespace App\Service;


use App\Model\Task;

class AgentTaskService extends AbstractService
{

    /**
     * @param int $page
     * @param int $size
     * @param int $source
     * @return array
     */
    public function getAgentTaskList(int $page, int $size,int $source): array
    {
        $total = Task::query()->where("source","=",$source)->count();

        $data = Task::query()->where("source","=",$source)->offset(($page - 1) * $size)->limit($size)->orderBy("sort", 'DESC')->orderBy("created_at", 'DESC')->select([
            'id', 'title', 'type','amount', 'platform','price','source','hot', 'status', 'review','sort', 'rule','teach_image','teach_video','number_limit','created_at'
        ])->get()->map(function ($item){
            $item->money =  ConfigService::staticGetTaskAllMoney($item->id) ?? "--";

            return $item;
        });

        return $data->isNotEmpty() ? [
            'total' => $total,
            'data'  => $data->toArray()
        ] : [];
    }

    /**
     * @param array $inputData
     * @param int $agent_app_id
     * @return bool
     */
    public function releaseAgentTask(array $inputData,int $agent_app_id):bool
    {
        $saveData = [];
        if (isset($inputData["title"]) && $inputData["title"])
        {
            $saveData["title"]  = $inputData["title"];
        }
        if (isset($inputData["amount"]) && $inputData["amount"])
        {
            $saveData["amount"]  = $inputData["amount"];
        }
        if (isset($inputData["price"]) && $inputData["price"])
        {
            $saveData["price"]  = $inputData["price"];
        }
        if (isset($inputData["rule"]) && $inputData["rule"])
        {
            $saveData["rule"]  = $inputData["rule"];
        }
        if (isset($inputData["teach_image"]) && $inputData["teach_image"])
        {
            $saveData["teach_image"]  = $inputData["teach_image"];
        }
        if (isset($inputData["teach_video"]) && $inputData["teach_video"])
        {
            $saveData["teach_video"]  = $inputData["teach_video"];
        }
        if (isset($inputData["number_limit"]) && $inputData["number_limit"])
        {
            $saveData["number_limit"]  = $inputData["number_limit"];
        }
        if (isset($inputData["type"]) && $inputData["type"])
        {
            $saveData["type"]  = $inputData["type"];
        }
        if (isset($inputData["hot"]) && $inputData["hot"])
        {
            $saveData["hot"]  = $inputData["hot"];
        }
        if (isset($inputData["platform"]) && $inputData["platform"])
        {
            $saveData["platform"]  = $inputData["platform"];
        }
        $saveData["source"] = $agent_app_id;
        $saveData["review"] = 2;
        $saveData["status"] = 0;

        $taskModel = new Task();

        foreach ($saveData as $k => $v) {
            $taskModel->setAttribute($k, $v);
        }
        return $taskModel->save();
    }

    /**
     * @param array $inputData
     * @param int $agent_app_id
     * @return bool
     */
    public function editAgentTask(array $inputData,int $agent_app_id): bool
    {
        $saveData = [];
        if (isset($inputData["id"]) && $inputData["id"])
        {
            $saveData["id"] = $inputData["id"];
        }else{
            return false;
        }
        if (isset($inputData["title"]) && $inputData["title"])
        {
            $saveData["title"]  = $inputData["title"];
        }
        if (isset($inputData["amount"]) && $inputData["amount"])
        {
            $saveData["amount"]  = $inputData["amount"];
        }
        if (isset($inputData["price"]) && $inputData["price"])
        {
            $saveData["price"]  = $inputData["price"];
        }
        if (isset($inputData["rule"]) && $inputData["rule"])
        {
            $saveData["rule"]  = $inputData["rule"];
        }
        if (isset($inputData["teach_image"]) && $inputData["teach_image"])
        {
            $saveData["teach_image"]  = $inputData["teach_image"];
        }
        if (isset($inputData["teach_video"]) && $inputData["teach_video"])
        {
            $saveData["teach_video"]  = $inputData["teach_video"];
        }
        if (isset($inputData["number_limit"]) && $inputData["number_limit"])
        {
            $saveData["number_limit"]  = $inputData["number_limit"];
        }
        if (isset($inputData["type"]) && $inputData["type"])
        {
            $saveData["type"]  = $inputData["type"];
        }
        if (isset($inputData["hot"]) && $inputData["hot"])
        {
            $saveData["hot"]  = $inputData["hot"];
        }
        if (isset($inputData["platform"]) && $inputData["platform"])
        {
            $saveData["platform"]  = $inputData["platform"];
        }

        $task = Task::query()->where([
            ["source","=",$agent_app_id],
            ["id","=",$saveData["id"]],
            ["review","=",2]
        ])->first();
        if ($task){
            foreach ($saveData as $k => $v)
            {
                $task->setAttribute($k,$v);
            }
            return  $task->save();
        }
        return false;
    }

}