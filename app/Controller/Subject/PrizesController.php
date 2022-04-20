<?php

declare(strict_types=1);

namespace App\Controller\Subject;
use App\Service\PrizesService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;
use App\Controller\Http\AbstractController;
use Throwable;

/**
 * Class PrizesController
 * @package App\Controller\Subject
 * @AutoController (prefix="api/Prizes")
 */
class PrizesController extends AbstractController
{

    /**
     * @Inject ()
     * @var PrizesService
     */

    protected $prizesService;

    /**
     * @GetMapping (path="getPrizesList")
     */
    public function getPrizesList(): ResponseInterface
    {
        $size =  $this->request->input("size",$this->size);
        $page =  $this->request->input("page",$this->page);

        $data =  $this->prizesService->getList(intval($page),intval($size));
        return $this->jsonResponse(200,'',$data);
    }



    /**
     * @PostMapping (path="savePrizesInfo")
     */
    public function savePrizesInfo(): ResponseInterface
    {

        $inputData = $this->request->all();
        if (isset($inputData["probability"])   && isset($inputData["status"]) && $inputData["status"])
        {
            if ($inputData["status"] == 1)
            {
                $verification = $this->prizesService->verificationProbability($inputData);
                if (!$verification)
                {
                    return $this->jsonResponse(401,"输入概率与其他奖品概率之和大于100%");
                }
            }
            $result = $this->prizesService->saveInfo($inputData);

            if ($result){ return  $this->jsonResponse(200,"",[]); }

            return  $this->jsonResponse(400,"",[]);
        }

        return $this->jsonResponse(400,"");
    }

    /**
     * @PostMapping (path="delPrzieInfo")
     */
    public function delPrzieInfo(): ResponseInterface
    {

        $id = $this->request->input("id");
        if (!$id)
        {
            return $this->jsonResponse(400,"",[]);
        }

        $result = $this->prizesService->delInfo(intval($id));
        if ($result)
        {
            return $this->jsonResponse(200,"");
        }
        return $this->jsonResponse(400,"");

    }

    /**
     * @PostMapping (path="uploads")
     * @return ResponseInterface
     */
    public function uploads(): ResponseInterface
    {
        try {
            $file = $this->request->file('file');
            if (!$file) {
                return $this->jsonResponse(400,'FILE_DOES_NOT_EXIST',[]);
            }
            $size = $file->getSize();
            if ($size / 1024 / 1024 > 10) {
                return $this->jsonResponse(400,'大小超过10M',[]);
            }
            $extName = $file->getExtension();

            $dir     = BASE_PATH . '/public/prizes/';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $fileName = md5(date('Y-m-d H:i:s') . mt_rand(10000, 99999));
            $path     = $dir . $fileName . '.' . $extName;
            $file->moveTo($path);
            chmod($dir.$fileName.'.'.$extName, 0755);
            return $this->jsonResponse(200,'',[
                'src' => 'prizes/'  . $fileName . '.' . $extName
            ]);
        } catch (Throwable $throwable) {
            return $this->jsonResponse($throwable->getCode(),$throwable->getMessage(),[]);
        }
    }

    /**
     * 用户获奖记录(backend)
     * @PostMapping (path="getMemberPrizesList")
     */
    public function getMemberPrizesList(): ResponseInterface
    {
        $page = $this->request->input('page',$this->page);
        $size = $this->request->input('size',$this->size);
        $pid  = $this->request->input('prize_id');
        $cid  = $this->request->input('cate_id');
        $start = $this->request->input("start");
        $end   = $this->request->input("end");

        $where = [];
        if ($start)
        {
            $where[] = ['created_at', '>=', $start];
        }
        if ($end)
        {
            $where[] = ['created_at', '<=', $end];
        }
        if (!is_null($pid))
        {
            $where[] = ["pid" , "=" , $pid];
        }
        if (!is_null($cid))
        {
            $where[] = ["cid" , "=" , $pid];
        }

        $data = $this->prizesService->memberAwardRecords(intval($page),intval($size),$where);

        return $this->jsonResponse(200,"",$data);

    }

}
