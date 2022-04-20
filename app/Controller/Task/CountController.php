<?php

declare(strict_types=1);
namespace App\Controller\Task;


use App\Service\CountService;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

use App\Controller\Http\AbstractController;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController(prefix="api/Count")
 */
class CountController extends AbstractController
{

    /**
     * @Inject()
     * @var CountService
     */
    protected $countService;


    /**
     * 获取当日用户总量
     * @GetMapping(path="getTaskMemberCount")
     */
    public function getTaskMemberCount(): ResponseInterface
    {
        //获取日期
        $date = $this->request->input('date', date('Y-m-d H:i:s', time()));

        $data = $this->countService->getMemberCount($date);

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取当月每日用户总量
     * @GetMapping(path="getMemberMonthCount")
     */
    public function getMemberMonthCount(): ResponseInterface
    {
        //获取日期
        $date = $this->request->input('date', date('Y-m-d H:i:s', time()));

        if ($date == "")
        {
            $date = date('Y-m-d H:i:s', time());
        }
        $data = $this->countService->getMemberMonthCount($date);

        return $this->jsonResponse(200,'',$data);
    }
    /**
     * 获取当日提现数据
     * @GetMapping(path="getTaskWithdrawCount")
     */
    public function getTaskWithdrawCount(): ResponseInterface
    {

        //获取日期
        $date = $this->request->input('date', date('Y-m-d H:i:s', time()));

        $data = $this->countService->getTaskWithdrawCount($date);

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取当日任务审核相关数据
     * @GetMapping(path="getTaskTodoCount")
     */
    public function getTaskTodoCount(): ResponseInterface
    {
        //获取日期
        $date = $this->request->input('date', date('Y-m-d H:i:s', time()));

        $data = $this->countService->getTaskTodoCount($date);

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取当日任务审核相关数据
     * @PostMapping(path="getMonthCountByTable")
     */
    public function getMonthCountByTable(): ResponseInterface
    {
        $table   = $this->request->input("table");
        $field   = $this->request->input("field");
        $date    = $this->request->input("date" , date('Y-m-d H:i:s', time()));
        $status  = $this->request->input("status");
        if ($table == "" && $field == "")
        {
            return  $this->jsonResponse(400);
        }

        $where = "";
        if ($status)
        {
            $where = "status = ".$status;
        }

        $data = $this->countService->getMonthCountByTable($field,$table,$date,$where);


        return $this->jsonResponse(200,"",$data);
    }



    /**
     * 获取总量
     * @GetMapping(path="getTaskAllCount")
     */
    public function getTaskAllCount(): ResponseInterface
    {
        $data = $this->countService->getTaskAllCount();

        return $this->jsonResponse(200,'',$data);
    }


    /**
     * 获取总量
     * @GetMapping(path="getUserTaskRanking")
     */
    public function getUserTaskRanking(): ResponseInterface
    {

        $list = $this->countService->getUserTaskRankingList();

        return $this->jsonResponse(200,'',$list);
    }
}