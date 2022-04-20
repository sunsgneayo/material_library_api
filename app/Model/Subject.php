<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id 
 * @property int $admin_id 
 * @property string $subject 
 * @property string $options 
 * @property string $answer 
 * @property int $status 
 * @property int $type 
 * @property int $sort 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at
 * @property-read \App\Model\User $admin_user
 */
class Subject extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subjects';
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
        'status' => 'integer',
        'type' => 'integer',
        'sort' => 'integer',
        "options" => "json",
        "subject" => "json",
        'created_at' => 'datetime',
        'updated_at' => 'datetime'];

    public function adminUser(): HasOne
    {
        return $this->hasOne(User::class,"id","admin_id")->select("id","username");
    }

    public function category(): HasOne
    {
        return $this->hasOne(SubjectsCategory::class,"id","category_id")->select("id","name");
    }
}