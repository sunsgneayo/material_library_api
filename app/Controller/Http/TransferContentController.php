<?php

declare(strict_types=1);
namespace App\Controller\Http;

use App\Service\TransferContentService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;
use App\Service\ChannelContentService;
/**
 * @AutoController(prefix="api/transfercontent")
 */
class TransferContentController extends AbstractController
{

    /**
     * @Inject()
     * @var TransferContentService
     */
    protected $transferContent;


    /**
     *获取图片列表
     * @PostMapping(path="getList")
     */
    public function getList(): ResponseInterface
    {

        $page = $this->request->input('page' , intval($this->page));
        $size = $this->request->input('size' , intval($this->size));

        $data = $this->transferContent->getTransferContentList(intval($page) , intval($size));

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
        $data = $this->transferContent->getTransferContentInfo(intval($id));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 保存修改数据（新增、修改）
     * @PostMapping(path="saveInfo")
     */
    public function saveInfo()
    {
        $input = $this->request->all();

        $data = $this->transferContent->saveTransferContentInfo($input);

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

        $data = $this->transferContent->deleteTransferContentInfo(intval($id));

        if ($data){
            return $this->jsonResponse(200,'',[]);
        }
        return $this->jsonResponse(201,'操作失败',[]);
    }


}