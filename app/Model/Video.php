<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Relations\HasOne;
/**
 * @property int $id 
 * @property int $cid 
 * @property string $title 
 * @property string $url 
 * @property string $cover 
 * @property int $clicks 
 * @property int $shares 
 * @property int $status 
 * @property int $sort 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Video extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video';
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
        'cid' => 'integer',
        "title" => 'json',
        "describe" => 'json',
        "url" => 'json',
        "cover" => 'json',
        'clicks' => 'integer',
        'shares' => 'integer',
        'status' => 'integer',
        'sort' => 'integer',
        'collects'=>"integer",
        'comments'=>"integer",
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function VideoCategory(): HasOne
    {
        return $this->hasOne(VideoCategory::class,"id","cid")->select("id","name");
    }
}