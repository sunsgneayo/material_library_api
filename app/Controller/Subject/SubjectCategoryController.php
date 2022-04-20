<?php

declare(strict_types=1);

namespace App\Controller\Subject;

use App\Controller\Http\AbstractController;
use App\Service\SubjectCateService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * @AutoController (prefix="api/SubjectCategory")
 */
class SubjectCategoryController extends AbstractController
{

    /**
     * @Inject ()
     * @var SubjectCateService
     */
    protected $subjectCateService;

    /**
     * @GetMapping (path="getSubjectCateList")
     */
    public function getSubjectCateList(): ResponseInterface
    {
        $page = $this->request->input("page",$this->page);
        $size = $this->request->input("size",$this->size);

        $list = $this->subjectCateService->getList(intval($page),intval($size));

        return $this->jsonResponse(200,'',$list);
    }

    /**
     * @PostMapping (path="setSubjectCateInfo")
     */
    public function setSubjectCateInfo():ResponseInterface
    {
        $inputData = $this->request->all();

        $result = $this->subjectCateService->setInfo($inputData);
        if ($result)
        {
            return $this->jsonResponse(200,"",[]);
        }

        return $this->jsonResponse(202,'',[]);
    }


    /**
     * @PostMapping (path="delSubjectCateInfo")
     */
    public function delSubjectCateInfo(): ResponseInterface
    {
        $id = $this->request->input("id");
        if ($id)
        {
            $isExistence = $this->subjectCateService->isExistenceSubject(intval($id));
            if (!$isExistence)
            {
                $result = $this->subjectCateService->delInfo(intval($id));
                if ($result)
                    return $this->jsonResponse(200,"",[]);
                return $this->jsonResponse(202,'',[]);
            }
            return $this->jsonResponse(201,"该分类下存在题目");
        }

        return  $this->jsonResponse(404,'');
    }

}
