<?php
declare(strict_types=1);

namespace App\Service;


use App\Controller\Http\FontStyleController;
use App\Model\Fontstyle;
use App\Model\Image;
use App\Model\Type;
use Hyperf\Di\Annotation\Inject;
class ChannelImageService extends AbstractService
{

    /**
     * @Inject()
     * @var Image
     */
    protected $typeImageModel;


    /**
     * @var array
     */
    protected $select = ['id','type_id','language_id','size_id','name','enable'];

    /**图片列表
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getChannelImageList(int $page,int $size,string $where): array
    {
        $list = Image::query()->select($this->select);
        $total = Image::query();
        if (isset($where) && $where)
        {
            $list->whereRaw($where);
            $total = $total->whereRaw($where);
        }
        $list = $list->offset(($page - 1) * $size )->limit($size)
            ->get()->map(function ($item){
                $type = [];
                $item_type = json_decode($item->type_id ?? '{}', true);
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
                $item->type_id     = json_decode($item->type_id ?? '{}') ?? [];
                $fonts = Fontstyle::query()->where('image_id',$item->id)->get();
                $item->fonts = $fonts;

                $item->source_id   = config('app.source')[$item->source_id] ?? null;
                $item->platform_id = config('app.platform')[$item->platform_id] ?? null;
                return $item;
            })->toArray();

        $total = $total->count();
        return [
            'total'  => $total,
            'data'   => $list
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    public function getChannelImageInfo(int $id)
    {
        $data = Image::query()->select($this->select)->where('id',$id)->get()->map(function ($item){
            $type = [];
            $item_type = json_decode($item->type_id ?? '{}' , true);

            for($i = 0; $i < count($item_type); $i++)
            {
                $type[$i] = Type::query()->where('id',$item_type[$i])->select('id','name')->first()->toArray();
            }
            $item->type = $type;

            $item->type_id = json_decode($item->type_id ?? '{}') ?? [];

            $fonts = Fontstyle::query()->where('image_id',$item->id)->get();
            $item->fonts = $fonts;
//            $item->font_shadow = json_decode($item->font_shadow) ?? [];
//            $item->font_stroke = json_decode($item->font_stroke) ?? [];

            return $item;
        })->toArray();

        return $data ?? [];
    }


    /**
     * @Inject()
     * @var FontStyleController
     */
    protected $fontStyle;

    /**保存、修改数据
     * @param array $inputData
     * @return bool
     */
    public function saveChannelImageInfo(array $inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['type_id']) && $inputData['type_id']){
            $saveData['type_id'] = json_encode($inputData['type_id']);
        }
        if (isset($inputData['size_id']) && $inputData['size_id']){
            $saveData['size_id'] = $inputData['size_id'];
        }
        if (isset($inputData['name']) && $inputData['name']){
            $saveData['name'] = $inputData['name'];
        }

        if (isset($inputData['enable']) && $inputData['enable']){
            $saveData['enable'] = $inputData['enable'];
        }

        $id  = $this->typeImageModel->saveInfo($saveData);
        if ($id){
            $font = $inputData['fonts'];
            if (count( $font ) > 0){
                 return $this->fontStyle->saveFontStyleInfo(intval($id),$font);
            }
//            return true;
        }
        return $id;

    }

    /**删除
     * @param int $id
     * @return bool
     */
    public function deleteChannelImageInfo(int $id)
    {
        $id = $this->typeImageModel->deleteInfo($id);
        if ($id)
        {
            return true;
        }

        return false;
    }
}