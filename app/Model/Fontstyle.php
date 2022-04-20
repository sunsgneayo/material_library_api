<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $image_id 
 * @property int $font 
 * @property string $font_color 
 * @property int $font_size 
 * @property string $font_position 
 * @property string $font_shadow 
 * @property string $font_stroke 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Fontstyle extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fontstyles';
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
        'id'           => 'integer',
        'image_id'     => 'integer',   //图片ID
        'font'         => 'integer',   //字体ID
        'font_size'    => 'integer',   //字体大小
        'font_shadow'  => 'json',      //字体阴影
        'font_stroke'  => 'json',      //字体描边
        'padding'      => 'json',      //背景边距
        'textShadow'   => 'json',      //背景阴影
        'background'   => 'string',    //背景色
        'width'        => 'integer',   // 背景宽度
        'textAlign'    => 'string',    // 字体对齐方式 左对齐(left) 居中(center) 右对齐(right)
        'font_rotate'  => 'integer',   // 旋转的度数
        'borderRadius' => 'integer',   // 背景圆角
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime'
    ];

}