<?php

declare(strict_types=1);
namespace App\Controller\Http;
use App\Service\ChannelImageService;
use App\Service\TransferImageService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController(prefix="api/transferimage")
 */
class TransferImageController extends AbstractController
{

    /**
     * @Inject()
     * @var TransferImageService
     */
    protected $transferImage;


    /**
     *获取图片列表
     * @PostMapping(path="getList")
     */
    public function getList(): ResponseInterface
    {

        $page = $this->request->input('page' , intval($this->page));
        $size = $this->request->input('size' , intval($this->size));

        $data = $this->transferImage->getTransferImageList(intval($page) , intval($size));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取渠道图片详情
     * @PostMapping(path="getInfo")
     */
    public function getInfo(): ResponseInterface
    {
        $id = $this->request->input('id');

        if (!$id)
        {
            return $this->jsonResponse(201 , '',[]);
        }
        $data = $this->transferImage->getTransferImageInfo(intval($id));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 保存修改数据（新增、修改）
     * @PostMapping(path="saveInfo")
     */
    public function saveInfo()
    {
        $input = $this->request->all();

        $data = $this->transferImage->saveTransferImageInfo($input);

        if ($data){
            return $this->jsonResponse(200,'',[]);
        }
        return $this->jsonResponse(201,'操作失败',[]);
    }

    /**
     * 数据删除
     * @PostMapping(path="deleteInfo")
     */
    public function deleteInfo()
    {
        $id = $this->request->input('id');
        if (!$id)
        {
            return $this->jsonResponse(201,'',[]);
        }

        $data = $this->transferImage->deleteTransferImageInfo(intval($id));

        if ($data){
            return $this->jsonResponse(200,'',[]);
        }
        return $this->jsonResponse(201,'操作失败',[]);
    }


}