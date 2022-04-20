<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $name 
 * @property string $type_id 
 * @property string $config 
 * @property int $status 
 * @property string $domain 
 * @property int $sort 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class TransferType extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transfer_type';
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
    protected $casts = ['id' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @param array $select
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0 , $select = [])
    {
        $query = $this->query()->select($select);
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($k === 'type_id') {
                $query = $query->whereRaw("JSON_SEARCH( `type_id`, 'one', '".$v."')");
                continue;
            }
            if ($k === 'name') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
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

            $images = TransferImage::query()->whereRaw("JSON_SEARCH( `transfer_type_id`, 'one', '". $item->id."')")
                ->select('id','name')
                ->get();
            $images ? $images->toArray() : [];
            $item->images = $images;
            //渠道类型
            $type = [];
            $item_type = json_decode($item->type_id , true);

            for($i = 0; $i < count($item_type); $i++)
            {
                $type[$i] = Type::query()->where('id',$item_type[$i])->select('id','name')->first();
                $type[$i] ? $type[$i]->toArray()  : [];
            }
            $item->type = $type;

            $item->domain = $item->domain ?? 'https://d3p80o5v88i7c7.cloudfront.net/ap/';
            return $item;
        });


        return $query ? $query->toArray() : [];
    }
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

            if ($k === 'type_id') {
                $query = $query->whereRaw("JSON_SEARCH( `type_id`, 'one', '".$v."')");
                continue;
            }
            if ($k === 'name') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }
}