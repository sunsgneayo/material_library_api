<?php

declare(strict_types=1);
namespace App\Controller\Http;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;
use App\Service\ChannelContentService;
/**
 * @AutoController(prefix="api/channelcontent")
 */
class ChannelContentController extends AbstractController
{

    /**
     * @Inject()
     * @var ChannelContentService
     */
    protected $channelContent;


    /**
     *获取图片列表
     * @PostMapping(path="getList")
     */
    public function getList(): ResponseInterface
    {

        $page = $this->request->input('page' , intval($this->page));
        $size = $this->request->input('size' , intval($this->size));

        $where = [];
        $type_id = $this->request->input("type_id");
        if (isset($type_id) && $type_id)
        {
            $where[] = ["type_id","=",$type_id];
        }

        $data = $this->channelContent->getChannelContentList(intval($page) , intval($size),$where);

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
        $data = $this->channelContent->getChannelContentInfo(intval($id));

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * 保存修改数据（新增、修改）
     * @PostMapping(path="saveInfo")
     */
    public function saveInfo()
    {
        $input = $this->request->all();


        $data = $this->channelContent->saveChannelContentInfo($input);

        if ($data){
            return $this->jsonResponse(200,'',[]);
        }
        return $this->jsonResponse(201,'操作失败',[]);
    }

    /**
     * 批量新增数据
     * @PostMapping(path="batchSaveContents")
     * @return ResponseInterface
     *
     */
    public function batchSaveContents()
    {
        $input = $this->request->input('contents');

        if (!$input)
        {
            return $this->jsonResponse(204,'参数错误',[]);
        }
        $data = $this->channelContent->batchSaveChannelContentInfo($input);

        if ($data){
            return $this->jsonResponse(200,'批量新增成功',[]);
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

        $data = $this->channelContent->deleteChannelContentInfo(intval($id));

        if ($data){
            return $this->jsonResponse(200,'',[]);
        }
        return $this->jsonResponse(201,'操作失败',[]);
    }


}