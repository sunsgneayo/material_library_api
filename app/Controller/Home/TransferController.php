<?php


namespace App\Controller\Home;


use App\Model\TransferContent;
use App\Model\TransferImage;
use App\Model\TransferType;
use App\Model\Type;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController(prefix="api/transfer")
 */
class TransferController extends AbstractController
{

    /**
     * 获取中转页文字内容
     * @PostMapping(path="getTransferContentInfo")
     */
    public function getTransferContentInfo(): ResponseInterface
    {
        $strip = $this->request->input('strip',1);
        $type_id = $this->request->input('type_id','');
        $data = [];
        if ($type_id){

            $transfer_type = TransferType::query()->whereRaw("JSON_SEARCH(`type_id`, 'one', '$type_id')")->value('id');

            $data =  TransferContent::query()->where('transfer_type_id' , $transfer_type)
                ->select('id','transfer_type_id','language_id','content')
                ->inRandomOrder()->take($strip)->get()->map(function ($item){
                    $item->language = config('app.lang')[$item->language_id];
                    return $item;
                })->toArray();
            if(!$data){
                $data = TransferContent::query()->select('id','transfer_type_id','language_id','content')->inRandomOrder()->take(1)->get()->toArray();
            }
        }
        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取中转页图片
     * @PostMapping(path="getTransferImagesInfo")
     */
    public function getTransferImagesInfo(): ResponseInterface
    {

        $type_id = $this->request->input('type_id');

        $list = [];
        if ($type_id) {
            $type = Type::query()->select('id', 'name', 'domain', 'config', 'foreign', 'status')
                ->where('id', '=', $type_id)
                ->where('status', '=', 1)
                ->first();

            $type = $type ? $type->toArray() : [];
            if (empty($type)) {
                return $this->jsonResponse(200, '渠道类型不可用', []);
            }


            $transfer_type = TransferType::query()->whereRaw("JSON_SEARCH(`type_id`, 'one', '$type_id')")
                ->where('status', 1)
                ->select('id', 'name', 'status','domain')->get()->map(function ($item){
                    $image =  TransferImage::query()->select('id', 'name')
                        ->where('name' , '!=' ,'')
                        ->whereRaw("JSON_SEARCH(`transfer_type_id`, 'one', '$item->id')")->get();
                    $item->images = $image;
                    return $item;
                });
            $transfer_type = $transfer_type ? $transfer_type->toArray() : [];

            if (empty($transfer_type)) {
                return $this->jsonResponse(200, '中转页类型不可用', []);
            }
            $list = [
                'type' => $type,
                'transfer_type' => $transfer_type,
            ];
        }
        return $this->jsonResponse(200,'',$list);
    }
}