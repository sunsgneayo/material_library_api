<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $admin_id 
 * @property string $path 
 * @property string $method 
 * @property string $ip 
 * @property string $input 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property-read \App\Model\User $admin_user
 */
class AdminOperationLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_operation_log';
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
    protected $casts = [
        'id' => 'integer',
        'admin_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        "input"      => "json"
        ];
    public function admin_user() : HasOne
    {
        return $this->hasOne(User::class, "id", "admin_id")->select("id", "username");
    }
}