<?php

declare(strict_types=1);

namespace App\Controller\SpeedMember;

use App\Controller\Http\AbstractController;
use App\Model\SpeedMembers;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MemberController
 * @package App\Controller\SpeedMember
 * @AutoController (prefix="api/speed")
 */
class MemberController extends AbstractController
{
    /**
     * @PostMapping (path="getMemberList")
     * @return ResponseInterface
     */
    public function getMemberList(): ResponseInterface
    {
        $page = $this->request->input("page",$this->page);
        $size = $this->request->input("size",$this->size);

        $username = $this->request->input("username");
        $lineAccount = $this->request->input("line");

        $where = [];
        if ($username)
        {
            $where[] = ["username" , "=" ,$username];
        }
        if($lineAccount)
        {
            $where[] = ["line_account" , "=" ,$lineAccount];
        }

        $data = SpeedMembers::query()->where($where)
            ->select('id','username','line_account','country','status','created_at')
            ->offset(($page - 1) * $size)->limit($size)->orderBy("created_at", 'DESC')
            ->get();
        $total = SpeedMembers::query()->where($where)->count();

        return $this->jsonResponse(200,'',["data" => $data->isNotEmpty() ? $data->toArray() : [] , "total" => $total]);


    }
}
