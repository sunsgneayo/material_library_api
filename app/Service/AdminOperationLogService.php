<?php

declare(strict_types=1);
namespace App\Service;


use App\Model\AdminOperationLog;
use Monolog\Handler\WhatFailureGroupHandler;
use PHPUnit\Util\Json;

class AdminOperationLogService extends AbstractService
{


    public function recordLog(int $admin_id , string $path , string $method ,array $input  ,string  $ip = "" ) : bool
    {
        if (!$admin_id)
        {
            return  false;
        }
        $log = new AdminOperationLog();
        $log->setAttribute("admin_id",$admin_id);
        $log->setAttribute("path",$path);
        $log->setAttribute("method",$method);
//        $log->setAttribute("ip",$ip);
        $log->setAttribute("input",$input);

        return $log->save();
    }

    public function getLogList(int $page,int $size,array $where = []):array
    {
        $count = AdminOperationLog::query()->where($where)->count();
        $data  = AdminOperationLog::query()->with("admin_user")
            ->where($where)->offset(($page-1) * $size )->limit($size)->orderBy("id","desc")->get();

        return  [
            "data"  => $data->isNotEmpty() ? $data->toArray() : [],
            "total" => $count
        ];
    }

}
