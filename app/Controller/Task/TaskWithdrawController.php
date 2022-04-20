<?php


namespace App\Controller\Task;


use App\Controller\Http\AbstractController;

use App\Service\AdminOperationLogService;
use App\Service\TaskWithdrawService;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @AutoController(prefix="api/TaskWithdraw")
 */
class TaskWithdrawController extends AbstractController
{

    /**
     * @Inject()
     * @var TaskWithdrawService
     */
    protected $taskWithdrawService;


    /**
     * @Inject()
     * @var AdminOperationLogService
     */
    protected $adminOperationLogService;

    /**
     * @GetMapping(path="getTaskWithdrawList")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getTaskWithdrawList()
    {
        $page  = $this->request->input('page', $this->page);
        $size  = $this->request->input('size', $this->size);
        $status = trim($this->request->input('status'));

        $where = [];
        if ($status != '')
        {
            $where[] = ['status','=' ,$status];
        }

        $data = $this->taskWithdrawService->getTaskWithdrawList($where,intval($page), intval($size));

        return $this->jsonResponse(200, '', $data);
    }

    /**
     * @PostMapping(path="examineTaskWithdraw")
     */
    public function examineTaskWithdraw()
    {
        $id = $this->request->input('id');
        if (!$id)
        {
            return $this->jsonResponse(202,'',[]);
        }

        $status = intval($this->request->input('status'));

        $data = $this->taskWithdrawService->examineTaskWithdraw(intval($id) , intval($status));

        if ($data === false)
        {
            return $this->jsonResponse(202,'');
        }
        $this->adminOperationLogService->recordLog(intval($this->admin_id) , $this->request->path(),"UPDATE",$this->request->all());
        return $this->jsonResponse(200,'');
    }

}