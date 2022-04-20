<?php


namespace App\Controller\Http;


use App\Model\Fontstyle;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
/**
 * @AutoController(prefix="api/fontStyle")
 */
class FontStyleController extends AbstractController
{

    /**
     * @Inject()
     * @var Fontstyle
     */
    protected $fontStyle;



    /**保存文字样式（包括修改）
     * @param array $inputData
     * @param int $image_id
     * @return \Psr\Http\Message\ResponseInterface
     *
     */
    public function saveFontStyleInfo(int $image_id , array $inputData)
    {


        $saveData = [];
        for ($i = 0; $i < count($inputData); $i++)
        {

            $saveData[$i]['image_id'] = $image_id;

            if (isset($inputData[$i]['id']) && $inputData[$i]['id']){
                $saveData[$i]['id'] = $inputData[$i]['id'];
            }
            if (isset($inputData[$i]['font']) && $inputData[$i]['font']){
                $saveData[$i]['font'] = $inputData[$i]['font'];
            }
            if (isset($inputData[$i]['font_color']) && $inputData[$i]['font_color']){
                $saveData[$i]['font_color'] = $inputData[$i]['font_color'];
            }
            if (isset($inputData[$i]['font_size']) && $inputData[$i]['font_size']){
                $saveData[$i]['font_size'] = $inputData[$i]['font_size'];
            }
            if (isset($inputData[$i]['font_position']) && $inputData[$i]['font_position']){
                $saveData[$i]['font_position'] = $inputData[$i]['font_position'];
            }
            if (isset($inputData[$i]['font_shadow']) && $inputData[$i]['font_shadow']){
                $saveData[$i]['font_shadow'] = $inputData[$i]['font_shadow'] ;
            }
            if (isset($inputData[$i]['font_stroke']) && $inputData[$i]['font_stroke']){
                $saveData[$i]['font_stroke'] = $inputData[$i]['font_stroke'] ;
            }

            if (isset($inputData[$i]['textShadow']) && $inputData[$i]['textShadow']){
                $saveData[$i]['textShadow'] = $inputData[$i]['textShadow'];
            }
            if (isset($inputData[$i]['background']) && $inputData[$i]['background']){
                $saveData[$i]['background'] = $inputData[$i]['background'];
            }
            if (isset($inputData[$i]['width']) && $inputData[$i]['width']){
                $saveData[$i]['width'] = $inputData[$i]['width'];
            }
            if (isset($inputData[$i]['textAlign']) && $inputData[$i]['textAlign']){
                $saveData[$i]['textAlign'] = $inputData[$i]['textAlign'];
            }
            if (isset($inputData[$i]['font_rotate']) && $inputData[$i]['font_rotate']){
                $saveData[$i]['font_rotate'] = $inputData[$i]['font_rotate'];
            }
            if (isset($inputData[$i]['borderRadius']) && $inputData[$i]['borderRadius']){
                $saveData[$i]['borderRadius'] = $inputData[$i]['borderRadius'];
            }
            if (isset($inputData[$i]['padding']) && $inputData[$i]['padding']){
                $saveData[$i]['padding'] =  $inputData[$i]['padding'];
            }


            $saveData[$i]["created_at"] = date('Y-m-d H:i:s',time());
            $saveData[$i]["updated_at"] = date('Y-m-d H:i:s',time());
            $id = $this->fontStyle->saveInfo($saveData[$i]);
            if (!$id)
            {
                return  false;
            }
        }

        return true;
    }

    /**删除文字样式
     * @PostMapping(path="deleteFontStyleInfo")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteFontStyleInfo()
    {
        $ids = $this->request->input('ids');

        $ids = explode(',',$ids);
        if (count($ids) < 1)
        {
            return $this->jsonResponse(202,'',[]);
        }

        for ($i = 0 ; $i < count($ids); $i++)
        {
            $res = $this->fontStyle->deleteInfo($ids[$i]);
            if (!$res)
            {
                return $this->jsonResponse(202,'',[]);
            }
        }

        return $this->jsonResponse(200,'',[]);
    }
}