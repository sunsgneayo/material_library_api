<?php

declare(strict_types=1);

namespace App\Controller\Http;

use App\Service\ChannelTypeService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;

use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @AutoController(prefix="api/channeltype")
 */
class ChannelTypeController extends AbstractController
{

    /**
     * @Inject()
     * @var ChannelTypeService
     */
    protected $channelType;


    /**
     *获取渠道列表
     * @PostMapping(path="getList")
     */
    public function getList(): ResponseInterface
    {

        $page = $this->request->input('page' , intval($this->page));
        $size = $this->request->input('size' , intval($this->size));
        $where = $this->request->input('where','{}');

        $data = $this->channelType->getChannelTypeList(intval($page) , intval($size),json_decode($where));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取渠道类型详情
     * @PostMapping(path="getInfo")
     */
    public function getInfo(): ResponseInterface
    {
        $id = $this->request->input('id');

        if (!$id)
        {
            return $this->jsonResponse(201 , '',[]);
        }
        $data = $this->channelType->getChannelTypeInfo(intval($id));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 保存修改数据（新增、修改）
     * @PostMapping(path="saveInfo")
     */
    public function saveInfo()
    {
        $input = $this->request->all();

        $data = $this->channelType->saveChannelTypeInfo($input);

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

        $data = $this->channelType->deleteChannelTypeInfo(intval($id));

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
        $data = $this->channelType->getChannelTypeSelectList();

        return $this->jsonResponse(200,'',$data);
    }


    /**
     * 流程查看
     * @PostMapping(path="getProcessInfo")
     */
    public function getProcessInfo()
    {
        $id = $this->request->input('id');
        if (!$id)
        {
            return $this->jsonResponse(201,'',[]);
        }
        $data = $this->channelType->getProcessInfo(intval($id));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 一条以上type类型数据
     * @PostMapping(path="getTypesConfigInfo")
     */
    public function getTypesConfigInfo()
    {
        $ids = $this->request->input('ids');
        if (!$ids)
        {
            return $this->jsonResponse(202,'参数错误',[]);
        }
        $ids = explode(',',$ids);

        if (count($ids) < 1)
        {
            return $this->jsonResponse(202,'参数错误',[]);
        }

        $data = $this->channelType->getChannelTypesInfo($ids);

        return $this->jsonResponse(200,'',$data);
    }

}