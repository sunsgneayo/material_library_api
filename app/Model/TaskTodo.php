<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $task_id 
 * @property int $member_id 
 * @property string $received_at 
 * @property int $status 
 * @property string $reason 
 * @property string $examined_at
 * @property string $submited_at
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class TaskTodo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_todo';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'task_id' => 'integer','quantity' => 'integer', 'member_id' => 'integer', 'status' => 'integer','annex' => 'json', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function task()
    {
        return $this->hasOne(Task::class, 'id', 'task_id')->select([
            'id', 'title', 'price', 'rule','type'
        ]);
    }

    public function member()
    {
        return $this->hasOne(TaskMember::class, 'id', 'member_id')->select([
            'id', 'username', 'truename', 'nickname', 'accounts',"line_accounts"
        ]);
    }

    /**
     * 获取附件url地址
     *
     * @param  string  $value
     * @return array
     */
    public function getAnnexAttribute($value) : array
    {
        return [
            'domain'  => 'http://ptimage.a148666.com/',
            'annex'   => $value ? json_decode($value) ?? [] : []
        ];
    }
}
