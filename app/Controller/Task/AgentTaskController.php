<?php

declare(strict_types=1);
namespace App\Controller\Task;


use App\Controller\Http\AbstractController;
use App\Service\AdminOperationLogService;
use App\Service\AgentTaskService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController (prefix="api/Agent")
 * Class AgentTaskController
 * @package App\Controller\Task
 */
class AgentTaskController extends AbstractController
{

    /**
     * @Inject
     * @var AgentTaskService
     */
    protected $agentTaskService;


    /**
     * @Inject
     * @var AdminOperationLogService
     */
    protected $adminOperationLogService;

    /**
     * @GetMapping (path="agentTaskList")
     */
    public function agentTaskList(): ResponseInterface
    {
        $user = $this->request->getAttribute('user');

        if (empty($user)) {
            return $this->jsonResponse(400, '');
        }

        $page = $this->request->input('page', $this->page);
        $size = $this->request->input('size', $this->size);

        $data = $this->agentTaskService->getAgentTaskList(intval($page), intval($size),intval($user["app_id"]));

        return $this->jsonResponse(200, '', $data);
    }

    /**
     * @PostMapping (path="releaseAgentTask")
     * @return ResponseInterface
     */
    public function releaseAgentTask(): ResponseInterface
    {
        $user = $this->request->getAttribute('user');

        if (empty($user)) {
            return $this->jsonResponse(400, '');
        }
        $inputData = $this->request->all();

        $data  =  $this->agentTaskService->releaseAgentTask($inputData,intval($user["app_id"]));
        if ($data)
        {
            $this->adminOperationLogService->recordLog(intval($user["app_id"]) , $this->request->path(),"INSERT",$this->request->all());
            return $this->jsonResponse(200,"",[]);
        }

        return $this->jsonResponse(400,'');
    }

    /**
     * @PostMapping (path="editAgentTask")
     * @return ResponseInterface
     */
    public function editAgentTask() :ResponseInterface
    {
        $user = $this->request->getAttribute('user');
        if (empty($user)) {
            return $this->jsonResponse(400, '');
        }
        $id  = $this->request->input("id");
        if (empty($id))
        {
            return $this->jsonResponse(400, '');
        }
        $inputData = $this->request->all();
        $data  =  $this->agentTaskService->editAgentTask($inputData,intval($user["app_id"]));
        if ($data)
        {
            $this->adminOperationLogService->recordLog(intval($user["app_id"]) , $this->request->path(),"UPDATE",$this->request->all());
            return $this->jsonResponse(200,"",[]);
        }

        return $this->jsonResponse(400,'');
    }
}