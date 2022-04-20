<?php

declare(strict_types=1);
namespace App\Controller\Task;


use App\Controller\Http\AbstractController;
use App\Service\AdminOperationLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * Class AdminOperationLogController
 *
 * @AutoController (prefix="api/AdminLog")
 */
class AdminOperationLogController extends AbstractController
{

    /**
     * @Inject()
     * @var AdminOperationLogService
     */
    protected $adminOperationLogService;


    /**
     * @GetMapping(path="getAdminLogList")
     * @return ResponseInterface
     */
    public function getAdminLogList(): ResponseInterface
    {

        $page = $this->request->input("page",$this->page);
        $size = $this->request->input("size",$this->size);

        $start = $this->request->input("start");
        $end   = $this->request->input("end");

        $where = [];
        if (isset($start) && $start)
        {
            $where[] = ['created_at', '>=', $start];
        }
        if (isset($end) && $end)
        {
            $where[] = ['created_at', '<=', $end];
        }

        $data = $this->adminOperationLogService->getLogList(intval($page) , intval($size),$where);

        return $this->jsonResponse(200,"",$data);
    }
}