<?php

declare(strict_types=1);

namespace App\Controller\Agent;
use App\Controller\Home\AbstractController;
use App\Service\PrizesService;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PrizeController
 * @package App\Controller\Agent
 * @AutoController (prefix="api/agent")
 */
class PrizeController extends AbstractController
{
    /**
     * @Inject ()
     * @var PrizesService
     */
    protected $prizesService;


    /**
     * @GetMapping (path="getMemberPrizesList")
     * @return ResponseInterface
     */
    public function getMemberPrizesList(): ResponseInterface
    {
        //$cid = $this->request->input("cid"); // 题库ID
        $page = $this->request->input("page", $this->page);
        $size = $this->request->input("size", $this->size);

        $agent_id = $this->request->input("agent_id");
        if (!$agent_id)
        {
            return  $this->jsonResponse(400,'');
        }
        $where = [
            ["agent_id" , "=" , $agent_id]
        ];
        $data = $this->prizesService->memberAwardRecords(intval($page),intval($size) , $where);

        return $this->jsonResponse(200,"",$data);
    }

}
