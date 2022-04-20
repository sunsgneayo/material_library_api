<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $type_id 
 * @property int $language_id 
 * @property string $content 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Content extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contents';
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
    protected $casts = ['id' => 'integer', 'type_id' => 'integer', 'language_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}