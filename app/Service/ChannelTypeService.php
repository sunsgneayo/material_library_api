<?php

declare(strict_types=1);
namespace App\Service;



use App\Model\Content;
use App\Model\Image;
use App\Model\TransferContent;
use App\Model\TransferImage;
use App\Model\TransferType;
use App\Model\Type;
use Hyperf\Di\Annotation\Inject;

class ChannelTypeService extends AbstractService
{
    /**
     * @Inject()
     * @var Type
     */
    protected $typeModel;

    /**
     * @var array
     */
    protected $select = ['id','name','domain','image','sort','status','config','foreign','describe'];
    /**获取渠道列表
     * @param int $page
     * @param int $size
     * @param array $where
     * @return array|\Hyperf\Utils\Collection
     */
    public function getChannelTypeList(int $page,int $size,$where = [] )
    {

//        $list = Type::query()
//            ->select($this->select)
//            ->offset(($page-1) * $size)->limit($size)->get()->map(function ($item){
//                $images = Image::query()->whereRaw("JSON_SEARCH( `type_id`, 'one', '". $item->id."')")
//                    ->select('id','name','font_color','font_size','font_position')
//                    ->get();
//                $item->images = $images;
//                $item->image = json_decode($item->image) ?? [];
//
//                //类型下的文字内容
//                $contents = Content::query()->where('type_id',$item->id)->select('id','language_id','content')->get();
//                $item->contents = $contents;
//
//                //类型下(渠道)下的中转页
//                $transfer = TransferType::query()->whereRaw("JSON_SEARCH( `type_id`, 'one', '". $item->id."')")->select('id','name')->get();
//
//                $item->transfer = $transfer;
//
//                return $item;
//            })->toArray();
//
//        $total = Type::query()->count('id');
//        return [
//            'total' => $total,
//            'data'  => $list
//        ];

        return [
            'total' => $this->typeModel->getCount($where),
            'data'  => $this->typeModel->getList($where,['id'=>'DESC'],($page-1)*$size,$size,$this->select)
        ];
    }

    /**获取详情渠道类型
     * @param int $id
     * @return array
     */
    public function getChannelTypeInfo(int $id)
    {
        $data = Type::query()->select($this->select)->where('id',$id)->get()->map(function ($item){

            $images = Image::query()->whereRaw("JSON_SEARCH( `type_id`, 'one', '". $item->id."')")
                ->select('id','name','font_color','font_size','font_position')
                ->get();
            $item->images = $images;
            $item->image = json_decode($item->image) ?? [];

            //类型下的文字内容
            $contents = Content::query()->where('type_id',$item->id)->select('id','language_id','content')->get();
            $item->contents = $contents;

            return $item;
        })->toArray();

        return $data ?? [];
    }

    /**
     * @param array $inputData
     * @return bool
     */
    public function saveChannelTypeInfo(array $inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['name']) && $inputData['name']){
            $saveData['name'] = $inputData['name'];
        }
        if (isset($inputData['domain']) && $inputData['domain']){
            $saveData['domain'] = $inputData['domain'];
        }
        if (isset($inputData['image']) && $inputData['image']){
            $saveData['image'] = json_encode($inputData['image']) ?? [] ;
        }
        if (isset($inputData['config']) && $inputData['config']){
            $saveData['config'] = $inputData['config'];
        }
        if (isset($inputData['foreign']) && $inputData['foreign'] || $inputData['foreign'] > -1){
            $saveData['foreign'] = $inputData['foreign'];
        }
        if (isset($inputData['describe']) && $inputData['describe']){
            $saveData['describe'] = $inputData['describe'];
        }
        if (isset($inputData['status']) && $inputData['status'] || $inputData['status'] > -1){
            $saveData['status'] = $inputData['status'];
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
    public function deleteChannelTypeInfo(int $id)
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
    public function getChannelTypeSelectList()
    {
        $list = Type::query()->pluck('name','id')->toArray();
        return $list;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getProcessInfo(int $id)
    {
        $data = Type::query()->select($this->select)->where('id',$id)->get()->map(function ($item){

            $images = Image::query()->whereRaw("JSON_SEARCH( `type_id`, 'one', '". $item->id."')")
                ->select('id','name','font_color','font_size','font_position')
                ->get();
            $item->images = $images;
            $item->image = json_decode($item->image ?? '') ?? [];

            //类型下的文字内容
            $contents = Content::query()->where('type_id',$item->id)->select('id','language_id','content')->get();
            $item->contents = $contents;

            //中转页
            $transfer = TransferType::query()->whereRaw("JSON_SEARCH( `type_id`, 'one', '". $item->id."')")
                ->select('id','name')->get()->map(function ($item){
                    //中转页图片
                    $transfer_images = TransferImage::query()->whereRaw("JSON_SEARCH( `transfer_type_id`, 'one', '". $item->id."')")
                        ->select('id','name')->get();
                    $item->transfer_images = $transfer_images;
                    //中转页文字内容
                    $transfer_content = TransferContent::query()->where('transfer_type_id',$item->id)
                        ->select('content','id')->get();
                    $item->transfer_content = $transfer_content;

                    return $item;
                });
            $item->transfer = $transfer;

            return $item;
        })->toArray();

        return $data ?? [];
    }

    /**根据id取得type类型的内容
     * @param array $ids
     * @return array
     */
    public function getChannelTypesInfo($ids = [])
    {
        $list = Type::query()->whereIn('id',$ids)->select('image','domain','config','name','describe')->get()
            ->map(function ($item){
                $item->image = json_decode($item->image ?? '') ?? [];
                return $item;
            })->toArray();

        return $list ?? [];
    }

}