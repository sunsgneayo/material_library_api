<?php

declare(strict_types=1);

namespace App\Controller\Agent;

use App\Controller\Home\AbstractController;
use App\Service\SubjectCateService;
use App\Service\SubjectServer;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SubjectController
 * @package App\Controller\Agent
 * @AutoController (prefix="api/agent")
 */
class SubjectController extends AbstractController
{
    /**
     * @Inject ()
     * @var SubjectServer
     */
   protected $subjectServer;

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

       $data = $this->subjectCateService->getList(intval($page),intval($size));

       return $this->jsonResponse(200,"",$data);
   }

    /**
     * @PostMapping (path="getSubjectList")
     */
   public function getSubjectList(): ResponseInterface
   {
       $cid = $this->request->input("category_id");
       $page = $this->request->input("page",$this->page);
       $size = $this->request->input("size",$this->size);
       if (!$cid)
       {
           return $this->jsonResponse(202,'');
       }
       $where =[ ["category_id","=",$cid]];
       $data = $this->subjectServer->getSubjectsList(intval($page),intval($size),$where);
       return $this->jsonResponse(200,"",$data);
   }
}
