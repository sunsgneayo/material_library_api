<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\TaskAgent;
use App\Model\User;

class UserService extends AbstractService
{
    /**
     * 根据用户名和密码获取用户数据
     * @param string $username
     * @param string $password
     * @return array
     */
    public function getUserByUsernameAndPassword(string $username, string $password): array
    {
        $data = User::query()->where([
            ['username', '=', $username],
            ['password', '=', md5($password)]
        ])->select([
            'id', 'username', 'truename'
        ])->first();
        return $data ? $data->toArray() : [];
    }

    public function agentLogin(string $username, int $app_id): array
    {
        $data = TaskAgent::query()->where([
            ["username","=",$username],
            ["app_id","=",$app_id],
        ])->first();
        if ($data)
        {
            if ($data->getAttribute("status") == 0)
            {
                //禁用
                return [];
            }

            return $data->toArray();
        }
        return [];
    }
}