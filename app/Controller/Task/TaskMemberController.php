<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Controller\Http\AbstractController;
use App\Service\AdminOperationLogService;
use App\Service\TaskMemberService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @AutoController(prefix="api/TaskMember")
 */
class TaskMemberController extends AbstractController
{
    /**
     * @Inject()
     * @var TaskMemberService
     */
    protected $taskMemberService;


    /**
     * @Inject()
     * @var AdminOperationLogService
     */
    protected $adminOperationLogService;

    /**
     * @GetMapping(path="getTaskMemberList")
     */
    public function getTaskMemberList()
    {
        $page = $this->request->input('page', $this->page);
        $size = $this->request->input('size', $this->size);


        $type    = $this->request->input('type');
        $account = $this->request->input('accounts');
        $where = [];
        if (!empty($type) && $type)
        {
            $where[] = ["type" , "=" , $type];
        }
        if (!empty($account) && $account)
        {
            $where[] = ["accounts" , "LIKE" , "%$account%"];
        }

        $data = $this->taskMemberService->getTaskMemberList(intval($page), intval($size),$where);

        return $this->jsonResponse(200, '', $data);
    }

    /**
     * @PostMapping(path="setTaskMemberPassword")
     */
    public function setTaskMemberPassword()
    {

        $id       = $this->request->input('id');

        $fields = [
            'status'   => intval($this->request->input('status')),
            'password' => $this->request->input('password')
        ];
        if (!$id)
        {
            return $this->jsonResponse(400,'',[]);
        }
        $data = $this->taskMemberService->setTaskMember(intval($id) , $fields);

        if ($data == false)
        {
            return $this->jsonResponse(400,'修改失败',[]);
        }
        $this->adminOperationLogService->recordLog(intval($this->admin_id) , $this->request->path(),"UPDATE",$this->request->all());
        return $this->jsonResponse(200,'',[]);

    }
}