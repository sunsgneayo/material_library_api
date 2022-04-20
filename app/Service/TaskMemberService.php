<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\TaskMember;

class TaskMemberService extends AbstractService
{
    /**
     * @param int $page
     * @param int $size
     * @param array $where
     * @return array
     */
    public function getTaskMemberList(int $page, int $size , array $where = []): array
    {
        $total = TaskMember::query()->where($where)->count();

        $data = TaskMember::query()->where($where)->offset(($page - 1) * $size)->limit($size)->orderByDesc('id')->select([
            'id', 'username', 'truename', 'nickname', 'money','accounts', 'type','aia_userid', 'line_accounts','status', 'created_at'
        ])->get();

        return $data->isNotEmpty() ? [
            'total' => $total,
            'data'  => $data->toArray()
        ] : [];
    }

    /**
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function setTaskMember(int $id, array $fields = [])
    {
        if (empty($fields)) {
            return false;
        }

        $data = TaskMember::query()->where([
            ['id', '=', $id]
        ])->first();

        if ($data) {
            foreach ($fields as $k => $v) {
                if ($v != '') {
                    if ($k == 'password') {
                        $v = md5($v);
                    }
                    $data->setAttribute($k, $v);
                }
            }

            return $data->save();
        } else {
            return false;
        }
    }
}