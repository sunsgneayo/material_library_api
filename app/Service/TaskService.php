<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Task;
use Exception;
use Hyperf\Di\Annotation\Inject;

class TaskService extends AbstractService
{
    /**
     * @Inject()
     * @var Task
     */
    protected $taskModel;



    public function getTaskList(int $page, int $size): array
    {
        $total = Task::query()->count();

        $data = Task::query()->offset(($page - 1) * $size)->limit($size)->orderBy("sort", 'DESC')->orderBy("created_at", 'DESC')->select([
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
    public function releaseTask(array $fields): bool
    {
        if (empty($fields)) {
            return false;
        }

        $taskModel = new Task();

        foreach ($fields as $k => $v) {
            $taskModel->setAttribute($k, $v);
        }

        return $taskModel->save();
    }

    public function editTask(int $id, array $fields): bool
    {
        if (empty($fields)) {
            return false;
        }

        $data = Task::query()->where([
            ['id', '=', $id]
        ])->first();

        if ($data) {
            foreach ($fields as $k => $v) {
                if ($v != '') {
                    $data->setAttribute($k, $v);
                }
            }

            return $data->save();
        } else {
            return false;
        }
    }

    public function delTask(int $id): bool
    {
        $data = Task::query()->where([
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
}