<?php

declare(strict_types=1);

namespace App\Controller\Subject;


use App\Controller\Http\AbstractController;
use App\Service\SubjectServer;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SubjectController
 * @package App\Controller\Subject
 *
 * @AutoController (prefix="api/subject")
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
     */
    public function getSubjectsList(): ResponseInterface
    {

        $page = $this->request->input("page",$this->page);
        $size = $this->request->input("size",$this->size);

        $cate_id = $this->request->input("cate_id");
        $where = [];
        if ($cate_id)
        {
            $where[] = ["category_id","=",$cate_id];
        }
        $data = $this->subjectServer->getSubjectsList(intval($page),intval($size),$where);

        return $this->jsonResponse(200,'',$data);
    }

    /**
     * @PostMapping (path="saveSubject")
     * @return ResponseInterface
     */
    public function saveSubject(): ResponseInterface
    {
        $input = $this->request->all();
        if (isset($input["id"]) && $input["id"])
        {
            $result = $this->subjectServer->saveSubject($this->admin_id,$input);
            if ($result)
            {
                return $this->jsonResponse(200,'');
            }
            return $this->jsonResponse(400,'');
        }else if(isset($input["subject"]) && $input["subject"] && isset($input["options"]) && $input["options"])
        {
            $result = $this->subjectServer->saveSubject($this->admin_id,$input);

            if ($result)
            {
                return $this->jsonResponse(200,'');
            }
            return $this->jsonResponse(400,'');
        }
        return $this->jsonResponse(400,'field is null');
    }
    /**
     * @PostMapping (path="batchSaveSubjects")
     * @return ResponseInterface
     */
    public function batchSaveSubjects(): ResponseInterface
    {
        $inputData = $this->request->all();

        $result = $this->subjectServer->batchSaveSubjects($this->admin_id,$inputData);
        if ($result)
        {
            return $this->jsonResponse(200,"");
        }
        return $this->jsonResponse(400,"");
    }

    /**
     * @PostMapping (path="delSubject")
     * @return ResponseInterface
     */
    public function delSubject(): ResponseInterface
    {
        $id = $this->request->input("id");
        if (!$id)
        {
            return $this->jsonResponse(400,"",[]);
        }

        $result = $this->subjectServer->delSubject(intval($id));
        if ($result)
        {
            return $this->jsonResponse(200,"");
        }
        return $this->jsonResponse(400,"");
    }
}
