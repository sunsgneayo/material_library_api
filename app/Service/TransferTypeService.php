<?php

declare(strict_types=1);
namespace App\Service;


use App\Model\TransferContent;
use App\Model\TransferImage;
use App\Model\TransferType;
use App\Model\Type;
use Hyperf\Di\Annotation\Inject;

class TransferTypeService extends AbstractService
{
    /**
     * @Inject()
     * @var TransferType
     */
    protected $typeModel;

    /**
     * @var array
     */
    protected $select = ['id','type_id','name','domain','sort','status','config'];

    /**中转页类型列表
     * @param int $page
     * @param int $size
     * @param array $where
     * @return array
     */
    public function getTransferTypeList(int $page,int $size ,array $where = []) : array
    {
//        $list = TransferType::query()
//            ->select($this->select)
//            ->offset($page-1)->limit($size)->get()->map(function ($item){
////                $images = TransferImage::query()->whereRaw("JSON_SEARCH( `transfer_type_id`, 'one', '". $item->id."')")
////                    ->select('id','name','font_color','font_size','font_position')
////                    ->get();
////                $item->images = $images;
////
////
////                //类型下的文字内容
////                $contents = TransferContent::query()->where('transfer_type_id',$item->id)->select('id','language_id','content')->get();
////                $item->contents = $contents;
////
////
////                //渠道类型
////                $type = [];
////                $item_type = json_decode($item->type_id , true);
////
////                for($i = 0; $i < count($item_type); $i++)
////                {
////                    $type[$i] = Type::query()->where('id',$item_type[$i])->select('id','name')->first()->toArray();
////                }
////                $item->type = $type;
////
////                return $item;
//            })->toArray();
//
//        $total = TransferType::count('id');
//        return [
//            'total' => $total,
//            'data'  => $list
//        ];

        return [
            'total' => $this->typeModel->getCount($where),
            'data'  => $this->typeModel->getList($where,['id'=>'DESC'],($page-1)*$size,$size,$this->select)
        ];
    }

    /**获取详情类型
     * @param int $id
     * @return array
     */
    public function getTransferTypeInfo(int $id)
    {
        $data = TransferType::query()
            ->select($this->select)
            ->where('id',$id)->get()->map(function ($item){
                $images = TransferImage::query()->whereRaw("JSON_SEARCH( `transfer_type_id`, 'one', '". $item->id."')")
                    ->select('id','name')
                    ->get();
                $item->images = $images;


                //类型下的文字内容
                $contents = TransferContent::query()->where('transfer_type_id',$item->id)->select('id','language_id','content')->get();
                $item->contents = $contents;


                //渠道类型
                $type = [];
                $item_type = json_decode($item->type_id , true);

                for($i = 0; $i < count($item_type); $i++)
                {
                    $type[$i] = Type::query()->where('id',$item_type[$i])->select('id','name')->first()->toArray();
                }
                $item->type = $type;

                return $item;
            })->toArray();

        return $data ?? [];
    }

    /**
     * @param array $inputData
     * @return bool
     */
    public function saveTransferTypeInfo(array $inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['name']) && $inputData['name']){
            $saveData['name'] = $inputData['name'];
        }
        if (isset($inputData['type_id']) && $inputData['type_id']){
            $saveData['type_id'] = json_encode($inputData['type_id']);
        }
        if (isset($inputData['status']) && $inputData['status'] || $inputData['status'] > -1){
            $saveData['status'] = $inputData['status'];
        }
        if (isset($inputData['config']) && $inputData['config']){
            $saveData['config'] = $inputData['config'];
        }
        if (isset($inputData['domain']) && $inputData['domain']){
            $saveData['domain'] = $inputData['domain'];
        }

        $id  = $this->typeModel->saveInfo($saveData);
        if ($id){
            return true;
        }
        return false;
    }

    /**删除
     * @param int $id
     * @return bool
     */
    public function deleteTransferTypeInfo(int $id)
    {
        $id = $this->typeModel->deleteInfo($id);
        if ($id)
        {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTransferTypeSelectList()
    {
        $list = TransferType::query()->pluck('name','id')->toArray();
        return $list;
    }
}