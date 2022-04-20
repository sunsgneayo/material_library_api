<?php

declare(strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class SpeedMembers extends Model
{

    protected $table = 'members';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    public $connection = 'speed';

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
//        'username' => 'stri',
//        'password' => 'integer',
//        'line_account' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
    ];

}