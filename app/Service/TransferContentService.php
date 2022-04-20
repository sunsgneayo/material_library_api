<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Image;


use App\Model\TransferContent;
use App\Model\TransferType;
use App\Model\Type;
use Hyperf\Di\Annotation\Inject;
use App\Model\Content;
class TransferContentService extends AbstractService
{

    /**
     * @Inject()
     * @var TransferContent
     */
    protected $typeContentModel;


    /**
     * @var array
     */
    protected $select = ['id','transfer_type_id','language_id','content'];

    /**图片列表
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getTransferContentList(int $page,int $size)
    {
        $list = TransferContent::query()->select($this->select)->offset(($page - 1)*$size)->limit($size)
            ->get()->map(function ($item){
                $item->type = TransferType::query()->where('id',$item->type_id)->select('id','name')->first();
                return $item;
            })->toArray();

        $total = TransferContent::count('id');
        return [
            'total' => $total,
            'data'  => $list
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    public function getTransferContentInfo(int $id)
    {
        $data = TransferContent::query()->select($this->select)->where('id',$id)->get()->map(function ($item){
            $item->type = TransferType::query()->where('id',$item->type_id)->select('id','name')->first();
            return $item;
        })->toArray();

        return $data ?? [];
    }

    /**保存、修改数据
     * @param array $inputData
     * @return bool
     */
    public function saveTransferContentInfo(array $inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['transfer_type_id']) && $inputData['transfer_type_id']){
            $saveData['transfer_type_id'] = $inputData['transfer_type_id'];
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

    /**删除
     * @param int $id
     * @return bool
     */
    public function deleteTransferContentInfo(int $id)
    {
        $id = $this->typeContentModel->deleteInfo($id);
        if ($id)
        {
            return true;
        }

        return false;
    }
}