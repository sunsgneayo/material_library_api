<?php

declare (strict_types=1);
namespace App\Model;

use App\Model\Base;
use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $parent_id 
 * @property string $name 
 * @property string $domain 
 * @property string $image 
 * @property int $sort 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 * @property int $status 
 * @property string $config 
 * @property int $foreign 
 */
class Type extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'types';
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
    protected $casts = ['id' => 'integer', 'parent_id' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'status' => 'integer', 'foreign' => 'integer'];


    /**
     * getList
     * 根据条件获取列表
     * User：YM
     * Date：2020/2/11
     * Time：下午9:22
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @param array $select 查询列
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0 , $select = [])
    {

        $query = $this->query()->select($select);
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($k === 'describe') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
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
            $images = Image::query()->whereRaw("JSON_SEARCH( `type_id`, 'one', '". $item->id."')")
                ->select('id','name','font_color','font_size','font_position')
                ->get();
            $item->images = $images;
            $item->image = json_decode($item->image ?? '') ?? [];

            //类型下的文字内容
            $contents = Content::query()->where('type_id',$item->id)->select('id','language_id','content')->get();
            $item->contents = $contents;

            //类型下(渠道)下的中转页
            $transfer = TransferType::query()->whereRaw("JSON_SEARCH( `type_id`, 'one', '". $item->id."')")->select('id','name')->get();

            $item->transfer = $transfer;

           // $item->domain = $item->domain ?? 'https://d3p80o5v88i7c7.cloudfront.net/ap/';
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

            if ($k === 'describe') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
            if ($k === 'name') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
//            $query = $query->where($this->table . '.' . $k, $v);
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }
}
