<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Model\Subject;

use App\Service\SubjectServer;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Di\Annotation\Inject;

/**
 * @AutoController(prefix="api")
 */
class SubjectController extends AbstractController
{
    /**
     * @Inject ()
     * @var SubjectServer
     */
    protected $subjectServer;

    /**
     * @GetMapping (path="getSubjectsList")
     * @return ResponseInterface
     */
    public function getSubjectsList(): ResponseInterface
    {
        $lang = $this->request->getAttribute('lang');
        $cate_id = $this->request->input('cate_id');
        $subject =  Subject::query()
            ->where("status","=",1)
            ->where("category_id","=",$cate_id)
//            ->inRandomOrder()->take(5)
            ->orderBy('sort',"DESC")
            ->select("id","subject","options","type","sort","category_id")
            ->get()->map(function ($item) use ($lang){
                $item->subject = $item->subject[$lang];
                $options = [];
                foreach ($item->options as $v)
                {
                    $options[] = $v[$lang];
                }
                $item->options = $options;
                return $item;
            });
        return $this->jsonResponse(200,"", $subject->isNotEmpty() ? $subject->toArray() : []);
    }
    /**
     * 提交答题结果
     * @PostMapping (path="setSubjects")
     */
    public function setSubjects(): ResponseInterface
    {

        $data = $this->request->input("data");
        $field = $this->decode($data);
        $uid = $field["uid"];
        $cid = $field["cid"];
        $option = $field["option"];

        if (!$uid && !$cid && !$option)
        {
            return $this->jsonResponse(404,'');
        }
        $data = $this->subjectServer->submitSubjectResult(intval($uid),intval($cid),$option);

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * @PostMapping (path="getMemberSubjects")
     * @return ResponseInterface
     */
    public function getMemberSubjects(): ResponseInterface
    {
        $data = $this->request->input("data");
        $field = $this->decode($data);
        $uid = $field["uid"];
        $cid = $field["cid"];

//        $cid = $this->request->input("cid");
//        $uid = $this->request->input("uid");

        if (!$uid && !$cid )
        {
            return $this->jsonResponse(404,'');
        }
        $data = $this->subjectServer->getMemberSubjectsInfo(intval($uid),intval($cid));

        return $this->jsonResponse(200,'',$data);
    }


    /**
     * @PostMapping (path="getRandomSubList")
     * @return ResponseInterface
     */
    public function getRandomSubList(): ResponseInterface
    {
        $lang    = $this->request->getAttribute('lang');
        $limit   = $this->request->input('limit' , 10);
        $subject =  Subject::query()->where("status","=",1)->inRandomOrder()->take($limit)
                   ->orderBy('sort',"DESC")
                   ->select("id","subject","options","type","sort","category_id")
                   ->get()->map(function ($item) use ($lang){
                        $item->subject = $item->subject[$lang];
                        $options = [];
                        foreach ($item->options as $v)
                        {
                            $options[] = $v[$lang];
                        }
                        $item->options = $options;
                        return $item;
                    });
        return $this->jsonResponse(200,"", $subject->isNotEmpty() ? $subject->toArray() : []);
    }
}
