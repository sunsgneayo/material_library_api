<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Controller\Http\AbstractController;
use App\Service\AdminOperationLogService;
use App\Service\TaskService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController(prefix="api/Task")
 */
class TaskController extends AbstractController
{
    /**
     * @Inject()
     * @var TaskService
     */
    protected $taskService;

    /**
     * @Inject()
     * @var AdminOperationLogService
     */
    protected $adminOperationLogService;


    /**
     * @GetMapping(path="getTaskList")
     */
    public function getTaskList(): ResponseInterface
    {
        $page = $this->request->input('page', $this->page);
        $size = $this->request->input('size', $this->size);

        $data = $this->taskService->getTaskList(intval($page), intval($size));

        return $this->jsonResponse(200, '', $data);
    }

    /**
     * @PostMapping(path="releaseTask")
     */
    public function releaseTask(): ResponseInterface
    {
        $fields = [
            'title'         => $this->request->input('title', ''),
            'amount'        => intval($this->request->input('amount', 0)),
            'price'         => $this->request->input('price', 0.00),
            'rule'          => $this->request->input('rule', ''),
            'status'        => $this->request->input('status') == 0 ? '0' : 1,
            'sort'          => intval($this->request->input('sort', 0)),
            'teach_image'   => $this->request->input('teach_image'),
            'teach_video'   => $this->request->input('teach_video'),
            'number_limit'  => $this->request->input('number_limit'),
            'type'          => intval($this->request->input('type')),
            'source'        => intval($this->request->input('source')),
            'hot'           => (string)($this->request->input('hot')),
            'platform'      => intval($this->request->input('platform')),
        ];

        $data = $this->taskService->releaseTask($fields);
        if ($data == false) {
            return $this->jsonResponse(400, '');
        }

        $this->adminOperationLogService->recordLog(intval($this->admin_id) , $this->request->path(),"INSERT",$this->request->all());

        return $this->jsonResponse(200, '');
    }

    /**
     * @PostMapping(path="editTask")
     */
    public function editTask(): ResponseInterface
    {
        $id = $this->request->input('id', '');

        if ($id == '') {
            return $this->jsonResponse(400, '');
        }
        $fields = [
            'title'         => $this->request->input('title', ''),
            'amount'        => intval($this->request->input('amount', 0)),
            'price'         => $this->request->input('price', 0.00),
            'rule'          => $this->request->input('rule', ''),
            'status'        => $this->request->input('status') == 0 ? '0' : 1,
            'sort'          => intval($this->request->input('sort', 0)),
            'teach_image'   => $this->request->input('teach_image'),
            'teach_video'   => $this->request->input('teach_video'),
            'number_limit'  => $this->request->input('number_limit'),
            'type'          => intval($this->request->input('type')),
            'source'        => intval($this->request->input('source')),
            'hot'           => (string)($this->request->input('hot')),
            'platform'      => intval($this->request->input('platform')),
            'review'        => intval($this->request->input('review')),
        ];
        $data = $this->taskService->editTask(intval($id), $fields);

        if ($data == false) {
            return $this->jsonResponse(400, '');
        }
        $this->adminOperationLogService->recordLog(intval($this->admin_id) , $this->request->path(),"UPDATE",$this->request->all());

        return $this->jsonResponse(200, '');
    }

    /**
     * @GetMapping(path="delTask")
     */
    public function delTask(): ResponseInterface
    {
        $id = $this->request->input('id', '');

        if ($id == '') {
            return $this->jsonResponse(400, '');
        }

        $data = $this->taskService->delTask(intval($id));

        if ($data == false) {
            return $this->jsonResponse(400, '');
        }

        $this->adminOperationLogService->recordLog(intval($this->admin_id) , $this->request->path(),"DELETE",$this->request->all());
        return $this->jsonResponse(200, '');
    }
}