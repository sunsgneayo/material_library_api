<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
use function Couchbase\defaultDecoder;

/**
 * @property int $id 
 * @property string $type_id 
 * @property int $language_id 
 * @property int $platform_id 
 * @property int $size_id 
 * @property string $name 
 * @property string $activity 
 * @property int $font 
 * @property string $font_color 
 * @property int $font_size 
 * @property string $font_position 
 * @property string $font_shadow 
 * @property string $font_stroke 
 * @property int $enable 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Image extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'images';
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
//        'type_id' => 'array',
        'language_id' => 'integer',
        'platform_id' => 'integer',
        'font' => 'integer',
        'font_size' => 'integer',
        'enable' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
//        'font_shadow' => 'json',
//        'font_stroke' => 'json'
    ];

//    public function setTypeIdAttribute($value)
//    {
//       $this->attributes['type_id'] = json_decode($value , true);
//    }
}