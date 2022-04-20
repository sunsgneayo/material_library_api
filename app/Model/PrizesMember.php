<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $pid 
 * @property int $uid 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class PrizesMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prizes_member';
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
    protected $casts = ['id' => 'integer', 'pid' => 'integer', 'uid' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function prize(): HasOne
    {
        return $this->hasOne(Prize::class, "id","pid")->select("name","id","image");
    }
    public function subjectCategory(): HasOne
    {
        return $this->hasOne(SubjectsCategory::class,"id","cid")->select("id","name");
    }
}