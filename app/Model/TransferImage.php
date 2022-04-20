<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $transfer_type_id 
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
class TransferImage extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transfer_images';
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
    protected $casts = ['id' => 'integer', 'language_id' => 'integer', 'platform_id' => 'integer', 'size_id' => 'integer', 'font' => 'integer', 'font_size' => 'integer', 'enable' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    /**
     * getCount
     * 用于条件查询计算总数
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {
        $query = $this->query();
        foreach ($where as $k => $v) {
//            if ($k === 'title') {
//                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
//                continue;
//            }
//            if ($k === 'start_time') {
//                $query = $query->where($this->table . '.published_time', '>', $v . ' 00:00:00');
//                continue;
//            }
//            if ($k === 'end_time') {
//                $query = $query->where($this->table . '.published_time', '<', $v . ' 23:59:59');
//                continue;
//            }
//            if ($k == 'category_ids') {
//                $query = $query->whereIn($this->table . '.category_id', $v);
//                continue;
//            }
            $query = $query->where($this->table . '.' . $k, $v);
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }
    /**
     * getList
     * 获取列表
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @param array $select
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0 ,$select)
    {
        $query = $this->query()->select($select);
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($k === 'title') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
//            if ($k === 'start_time') {
//                $query = $query->where($this->table . '.published_time', '>', $v . ' 00:00:00');
//                continue;
//            }
//            if ($k === 'end_time') {
//                $query = $query->where($this->table . '.published_time', '<', $v . ' 23:59:59');
//                continue;
//            }
//            if ($k == 'category_ids') {
//                $query = $query->whereIn($this->table . '.category_id', $v);
//                continue;
//            }
//            if ($v || $v != null) {
//                $query = $query->where($this->table . '.' . $k, $v);
//            }
        }
        // 追加排序
        if ($order && is_array($order)) {
            foreach ($order as $k => $v) {
                $query = $query->orderBy($this->table . '.' . $k, $v);
            }
        }
        // 是否分页
        if ($limit) {
            $query = $query->offset($offset)->limit($limit);
        }
        $query = $query->get()->map(function ($item){
            $type = [];
            $item_type = json_decode($item->type_id , true);
            for($i = 0; $i < count($item_type); $i++)
            {
                $type[$i] = Type::query()->where('id',$item_type[$i])->select('id','name')->first();
            }
            $item->type = $type;

            $type_id = [];
            for ($j = 0; $j < count($type); $j++)
            {
                $type_id[$type[$j]['id']]  =  $type[$j]['name'];
            }
            $item->type_id_arr     = $type_id;
            $item->type_id     = json_decode($item->type_id) ?? [];
            $fonts = Fontstyle::query()->where('image_id',$item->id)->get();
            $item->fonts = $fonts;

            $item->source_id   = config('app.source')[$item->source_id] ?? null;
            $item->platform_id = config('app.platform')[$item->platform_id] ?? null;
            return $item;
        });




        return $query ? $query->toArray() : [];
    }
}