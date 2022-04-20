<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

abstract class Base extends Model
{

    /**
     * saveInfo
     * 创建/修改记录
     * User：YM
     * Date：2020/1/8
     * Time：下午7:49
     * @param $data
     * @param bool $type 是否强制写入，适用于主键是规则生成情况
     * @return null
     */
    public function saveInfo($data,$type=false)
    {
        $id = null;
        $instance = make(get_called_class());
        if (isset($data['id']) && $data['id'] && !$type) {
            $id = $data['id'];
            unset($data['id']);
            $query = $instance->query()->find($id);
            foreach ($data as $k => $v) {
                $query->$k = $v;
            }
            $query->save();
        } else {
            foreach ($data as $k => $v) {
                if ($k === 'id') {
                    $id = $v;
                }
                $instance->$k = $v;
            }
            $instance->save();
            if (!$id) {
                $id = $instance->id;
            }
        }

        return $id;
    }

    public function batchSaveInfo($data)
    {
        $instance = make(get_called_class());
        $res = $instance->insert($data);
        return $res;
    }


    /**
     * deleteInfo
     * 删除/恢复
     * User：YM
     * Date：2020/2/3
     * Time：下午8:22
     * @param $ids
     * @param string 删除delete/恢复restore
     * @return int
     */
    public function deleteInfo($ids, $type = 'delete') {
        $instance = make(get_called_class());
        if ($type == 'delete') {
            return $instance->destroy($ids);
        } else {
            $count = 0;
            $ids = is_array($ids)?$ids:[$ids];
            foreach ($ids as $id) {
                if ($instance::onlyTrashed()->find($id)->restore()) {
                    ++$count;
                }
            }

            return $count;
        }
    }
}