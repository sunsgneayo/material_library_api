<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Image;

use App\Model\TransferImage;
use App\Model\TransferType;
use App\Model\Type;
use Hyperf\Di\Annotation\Inject;
class TransferImageService extends AbstractService
{

    /**
     * @Inject()
     * @var TransferImage
     */
    protected $typeImageModel;


    /**
     * @var array
     */
    protected $select = ['id','transfer_type_id','language_id','size_id','name','enable'];

    /**图片列表
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getTransferImageList(int $page,int $size)
    {
        $list = TransferImage::query()->select($this->select)->offset(($page-1) * $size)->limit($size)->orderBy('id','DESC')
            ->get()->map(function ($item){
                $type = [];
                $item_type = json_decode($item->transfer_type_id  ?? '', true) ?? [];
                for($i = 0; $i < count($item_type); $i++)
                {
                    $type[$i] = TransferType::query()->where('id',$item_type[$i])->select('id','name')->first();
                    $type[$i] = $type[$i] ? $type[$i]->toArray() : [];
                }
                $item->type = $type;

                $item->transfer_type_id = json_decode($item->transfer_type_id ?? '') ?? [];
//                $item->font_shadow = json_decode($item->font_shadow ?? '') ?? [];
//                $item->font_stroke = json_decode($item->font_stroke ?? '') ?? [];
                return $item;
            })->toArray();

        $total = TransferImage::count('id');
        return [
            'total' => $total,
            'data'  => $list
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    public function getTransferImageInfo(int $id)
    {
        $data = TransferImage::query()->select($this->select)->where('id',$id)->get()->map(function ($item){
            $type = [];
            $item_type = json_decode($item->transfer_type_id , true);

            for($i = 0; $i < count($item_type); $i++)
            {
                $type[$i] = TransferType::query()->where('id',$item_type[$i])->select('id','name')->first();

                $type[$i] = $type[$i] ? $type[$i]->toArray() : [];
            }
            $item->type = $type;

            $item->transfer_type_id = json_decode($item->transfer_type_id) ?? [];
            return $item;
        })->toArray();

        return $data ?? [];
    }

    /**保存、修改数据
     * @param array $inputData
     * @return bool
     */
    public function saveTransferImageInfo(array $inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['transfer_type_id']) && $inputData['transfer_type_id']){
            $saveData['transfer_type_id'] = json_encode($inputData['transfer_type_id']);
        }
        if (isset($inputData['size_id']) && $inputData['size_id']){
            $saveData['size_id'] = $inputData['size_id'];
        }
        if (isset($inputData['name']) && $inputData['name']){
            $saveData['name'] = $inputData['name'];
        }
//        if (isset($inputData['font']) && $inputData['font']){
//            $saveData['font'] = $inputData['font'];
//        }
//        if (isset($inputData['font_color']) && $inputData['font_color']){
//            $saveData['font_color'] = $inputData['font_color'];
//        }
//        if (isset($inputData['font_size']) && $inputData['font_size']){
//            $saveData['font_size'] = $inputData['font_size'];
//        }
//        if (isset($inputData['font_position']) && $inputData['font_position']){
//            $saveData['font_position'] = $inputData['font_position'];
//        }
//        if (isset($inputData['font_shadow']) && $inputData['font_shadow']){
//            $saveData['font_shadow'] = $inputData['font_shadow'] ;
//        }
//        if (isset($inputData['font_stroke']) && $inputData['font_stroke']){
//            $saveData['font_stroke'] = $inputData['font_stroke'];
//        }
//        if (isset($inputData['enable']) && $inputData['enable']){
//            $saveData['enable'] = $inputData['enable'];
//        }

        $id  = $this->typeImageModel->saveInfo($saveData);

        if ($id){
            return true;
        }
        return false;

    }

    /**删除
     * @param int $id
     * @return bool
     */
    public function deleteTransferImageInfo(int $id)
    {
        $id = $this->typeImageModel->deleteInfo($id);
        if ($id)
        {
            return true;
        }

        return false;
    }
}