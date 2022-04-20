<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Model\Prize;
use App\Service\PrizesService;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Di\Annotation\Inject;
/**
 * @AutoController(prefix="api")
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
     * @return ResponseInterface
     */
    public function getPrizesList(): ResponseInterface
    {
        $lang = $this->request->getAttribute('lang');
        $subject = Prize::query()
            ->where("status", "=", 1)
            ->inRandomOrder()->take(8)->orderBy('sort', "DESC")
            ->select("id", "name", "image", "sort", "probability")
            ->get()->map(function ($item) use ($lang) {
                $item->name = $item->name[$lang];
//                $item->domain = "http://192.168.1.6:8888";
                $item->domain = "http://www.craw248000.com/";
                return $item;
            });
        return $this->jsonResponse(200, "", $subject->isNotEmpty() ? $subject->toArray() : []);
    }

    /**
     * 抽奖
     * @GetMapping (path="getPrizeProbability")
     * @return ResponseInterface
     */
    public function getPrizeProbability(): ResponseInterface
    {
//        $uid = $this->request->input("uid");
//        $cid = $this->request->input("cid");
        $data = $this->request->input("data");
        $field = $this->decode($data);
        $uid = $field["uid"];
        $username = $field["username"];
        $cid = $field["cid"];
        $agent_id = $field["agent_id"];

        if (!$uid && !$cid && !$username && !$agent_id)
        {
            return $this->jsonResponse(404,"参数错误");
        }
        $data = $this->prizesService->getLuckydrawResult(intval($cid),intval($uid),intval($agent_id),$username);
        return  $this->jsonResponse(200,'',$data);
    }

    /**
     * 获取用户抽奖记录
     * @PostMapping (path="getMemberPrizeLog")
     */
    public function getMemberPrizeLog(): ResponseInterface
    {
//        $uid = $this->request->input("uid");
//        $cid = $this->request->input("cid");
//        $page = $this->request->input("page",$this->page);
//        $size = $this->request->input("size",$this->size);
        $data = $this->request->input("data");
        $field = $this->decode($data);
        $uid = $field["uid"];
        $cid = $field["cid"];
        $page = $field["page"] ?? $this->page;
        $size = $field["size"] ?? $this->size;

        if (!$uid && !$cid)
        {
            return $this->jsonResponse(404,"参数错误");
        }
        $where = [
            ["uid","=",$uid],
            ["cid","=",$cid],
        ];
        $data = $this->prizesService->memberAwardRecords(intval($page),intval($size),$where);

        return $this->jsonResponse(200,"",$data);
    }
}
