<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $title 
 * @property int $amount 
 * @property string $price 
 * @property string $rule 
 * @property int $status 
 * @property int $sort 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Task extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task';
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
        'id'          => 'integer',
        'amount'      => 'integer',
        'status'      => 'integer',
        'sort'        => 'integer',
        'title'       => 'json',
        'rule'        => 'json',
        'teach_image' => 'json',
        'teach_video' => 'json',
        'number_limit'=> 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'type'        => 'integer'
    ];
}