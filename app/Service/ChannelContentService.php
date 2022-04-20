<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Image;


use App\Model\Type;
use Hyperf\Di\Annotation\Inject;
use App\Model\Content;
class ChannelContentService extends AbstractService
{

    /**
     * @Inject()
     * @var Content
     */
    protected $typeContentModel;


    /**
     * @var array
     */
    protected $select = ['id','type_id','language_id','content'];

    /**图片列表
     * @param int $page
     * @param int $size
     * @param array $where
     * @return array
     */
    public function getChannelContentList(int $page,int $size,array $where): array
    {
        $list = Content::query()->where($where)->select($this->select)->offset(($page - 1) * $size)->limit($size)
            ->get()->map(function ($item){
                $item->type = Type::query()->where('id',$item->type_id)->select('id','name')->first();
                return $item;
            })->toArray();

        $total = Content::query()->where($where)->count();
        return [
            'total' => $total,
            'data'  => $list
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    public function getChannelContentInfo(int $id)
    {
        $data = Content::query()->select($this->select)->where('id',$id)->get()->map(function ($item){
            $item->type = Type::query()->where('id',$item->type_id)->select('id','name')->first();
            return $item;
        })->toArray();

        return $data ?? [];
    }

    /**保存、修改数据
     * @param array $inputData
     * @return bool
     */
    public function saveChannelContentInfo(array $inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['type_id']) && $inputData['type_id']){
            $saveData['type_id'] = $inputData['type_id'];
        }
        if (isset($inputData['language_id']) && $inputData['language_id']){
            $saveData['language_id'] = $inputData['language_id'];
        }
        if (isset($inputData['content']) && $inputData['content']){
            $saveData['content'] = $inputData['content'];
        }
        $id  = $this->typeContentModel->saveInfo($saveData);

        if ($id){
            return true;
        }
        return false;

    }

    public function batchSaveChannelContentInfo(array $inputData)
    {
        $saveData = [];
        for ($i = 0; $i < count($inputData); $i++)
        {
            if (isset($inputData[$i]['id']) && $inputData[$i]['id']){
                $saveData[$i]['id'] = $inputData[$i]['id'];
            }
            if (isset($inputData[$i]['type_id']) && $inputData[$i]['type_id']){
                $saveData[$i]['type_id'] = $inputData[$i]['type_id'];

            }
            if (isset($inputData[$i]['language_id']) && $inputData[$i]['language_id']){
                $saveData[$i]['language_id'] = $inputData[$i]['language_id'];
            }
            if (isset($inputData[$i]['content']) && $inputData[$i]['content']){
                $saveData[$i]['content'] = $inputData[$i]['content'];
            }
            $saveData[$i]["created_at"] = date('Y-m-d H:i:s',time());
            $saveData[$i]["updated_at"] = date('Y-m-d H:i:s',time());
        }

        $id  = $this->typeContentModel->batchSaveInfo($saveData);

        if ($id){
            return true;
        }
        return false;
    }

    /**删除
     * @param int $id
     * @return bool
     */
    public function deleteChannelContentInfo(int $id)
    {
        $id = $this->typeContentModel->deleteInfo($id);
        if ($id)
        {
            return true;
        }

        return false;
    }
}