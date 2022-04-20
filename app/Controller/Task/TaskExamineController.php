<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Controller\Http\AbstractController;
use App\Service\AdminOperationLogService;
use App\Service\TaskExamineService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @AutoController(prefix="api/TaskExamine")
 */
class TaskExamineController extends AbstractController
{
    /**
     * @Inject()
     * @var TaskExamineService
     */
    protected $taskExamineService;


    /**
     * @Inject()
     * @var AdminOperationLogService
     */
    protected $adminOperationLogService;

    /**
     * @GetMapping(path="getTaskExamineList")
     */
    public function getTaskExamineList()
    {
        $page = $this->request->input('page', $this->page);
        $size = $this->request->input('size', $this->size);

        $map = [];

        $status       = $this->request->input('status', '');
        $start        = $this->request->input('start', '');
        $end          = $this->request->input('end', '');
        $member_id    = $this->request->input('member_id');
        $task_id      = $this->request->input('task_id');
        $task_title   = $this->request->input('task_title','');

        if ($status != '') {
            $map[] = ['status', '=', $status];
        }

        if ($start != '') {
            $map[] = ['received_at', '>=', $start];
        }

        if ($end != '') {
            $map[] = ['received_at', '<=', $end];
        }
        if ($member_id != '')
        {
            $map[] = ['member_id', '=', $member_id];
        }
        if ($task_id != '')
        {
            $map[] = ['task_id', '=', $task_id];
        }


        $data = $this->taskExamineService->getTaskExamineList(intval($page), intval($size), $map,$task_title);

        return $this->jsonResponse(200, '', $data);
    }

    /**
     * @PostMapping(path="examineTask")
     */
    public function examineTask()
    {
        $id     = $this->request->input('id', '');
        $status = $this->request->input('status', '');
        $reason = $this->request->input('reason', '');
//        if (empty($this->request->input('quantity')))
//        {
//            return $this->jsonResponse(400, '');
//        }
        $quantity  = intval( $this->request->input('quantity'));

        if ($id == '') {
            return $this->jsonResponse(400, '');
        }

        $data = $this->taskExamineService->examineTask(intval($id), intval($status), trim($reason),intval($quantity));

        if ($data == false) {
            return $this->jsonResponse(400, '');
        }

        $this->adminOperationLogService->recordLog(intval($this->admin_id) , $this->request->path(),"UPDATE",$this->request->all());
        return $this->jsonResponse(200, '');
    }
}