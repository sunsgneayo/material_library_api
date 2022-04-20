<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $username 
 * @property string $password 
 * @property string $truename 
 * @property string $nickname 
 * @property string $accounts 
 * @property int $status 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class TaskMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_member';
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
    protected $casts = ['id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    public function todo()
    {
        return $this->belongsTo(TaskTodo::class, 'id', 'member_id')->with(['task']);
    }
}