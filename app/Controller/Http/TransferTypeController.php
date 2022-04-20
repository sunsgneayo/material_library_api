<?php

declare(strict_types=1);

namespace App\Controller\Http;

use App\Service\ChannelTypeService;
use App\Service\TransferTypeService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;

use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @AutoController(prefix="api/transfertype")
 */
class TransferTypeController extends AbstractController
{

    /**
     * @Inject()
     * @var TransferTypeService
     */
    protected $transferType;


    /**
     *获取Transfer列表
     * @PostMapping(path="getList")
     */
    public function getList(): ResponseInterface
    {

        $page  = $this->request->input('page' , intval($this->page));
        $size  = $this->request->input('size' , intval($this->size));
        $where = $this->request->input('where' , '{}');

        $data = $this->transferType->getTransferTypeList(intval($page) , intval($size),json_decode($where  ,true) ?? []);

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取Transfer类型详情
     * @PostMapping(path="getInfo")
     */
    public function getInfo(): ResponseInterface
    {
        $id = $this->request->input('id');

        if (!$id)
        {
            return $this->jsonResponse(201 , '',[]);
        }
        $data = $this->transferType->getTransferTypeInfo(intval($id));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 保存修改数据（新增、修改）
     * @PostMapping(path="saveInfo")
     */
    public function saveInfo()
    {
        $input = $this->request->all();

        $data = $this->transferType->saveTransferTypeInfo($input);

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

        $data = $this->transferType->deleteTransferTypeInfo(intval($id));

        if ($data){
            return $this->jsonResponse(200,'',[]);
        }
        return $this->jsonResponse(201,'操作失败',[]);
    }


    /**
     * select下拉列表
     * @PostMapping(path="getSelectList")
     */
    public function getSelectList()
    {
        $data = $this->transferType->getTransferTypeSelectList();

        return $this->jsonResponse(200,'',$data);
    }
}