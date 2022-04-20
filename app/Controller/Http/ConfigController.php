<?php


namespace App\Controller\Http;
use App\Service\ConfigService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @AutoController(prefix="api/Config")
 */
class ConfigController extends AbstractController
{


    /**
     * @Inject()
     * @var ConfigService
     */
    protected $configService;


    /**
     *获取来源列表
     * @PostMapping(path="getSourceList")
     */
    public function getSourceList()
    {
        $source = config('app.source') ?? [];

        return $this->jsonResponse(200,'',$source);
    }

    /**
     *获取语系列表
     * @PostMapping(path="getLanguageList")
     */
    public function getLanguageList()
    {
        $language = config('app.lang') ?? [];
        return $this->jsonResponse(200,'',$language);
    }

    /**
     *获取分享平台列表
     * @PostMapping(path="getPlatformList")
     */
    public function getPlatformList()
    {
        $data = config('app.platform') ?? [];
        return $this->jsonResponse(200,'',$data);
    }

    /**
     *获取语系列表
     * @PostMapping(path="getFontList")
     */
    public function getFontList()
    {
        $data = config('app.font') ?? [];
        return $this->jsonResponse(200,'',$data);
    }


    /**
     *
     * @PostMapping(path="getTaskAllMoney")
     */
    public function getTaskAllMoney()
    {

        $task_id = $this->request->input('task_id');
        if (!$task_id)
        {
            return $this->jsonResponse(400);
        }

        $data =  $this->configService->getTaskAllMoney(intval($task_id));
        return  $this->jsonResponse(200,'',$data);

    }
}