<?php

declare(strict_types=1);

namespace App\Controller\Http;

use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use League\Flysystem\Filesystem;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @AutoController(prefix="api/Common")
 */
class CommonController extends AbstractController
{
    /**
     * @Inject()
     * @var JWT
     */
    protected $jwt;

    /**
     * @Inject()
     * @var UserService
     */
    protected $userService;

    /**
     * 用户登录
     * @PostMapping(path="login")
     */
    public function login(): ResponseInterface
    {
        $username = $this->request->input('username', '');
        $password = $this->request->input('password', '');


        if ($username == '' || $password == '') {
            return $this->jsonResponse(400, '账号或密码错误');
        }

        $data = $this->userService->getUserByUsernameAndPassword(trim($username), trim($password));


        if (empty($data)) {
            return $this->jsonResponse(400, '账号或密码错误');
        }

        try {
            $token = $this->jwt->getToken($data);
            $exp   = $this->jwt->getTTL();

            return $this->jsonResponse(200, '登录成功', [
                'token' => (string)$token,
                'exp'   => $exp
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->jsonResponse(400, $e->getMessage());
        }
    }

    /**
     * 代理登录
     * @PostMapping (path="agentLogin")
     * @return ResponseInterface
     */
    public function agentLogin(): ResponseInterface
    {

        $userId   = $this->request->input('userId', '');
        $userName = $this->request->input('userName', '');

        if ($userId == '' || $userName == '') {
            return $this->jsonResponse(400, '');
        }

        $data = $this->userService->agentLogin(trim($userName),intval($userId));

        if (empty($data)) {
            return $this->jsonResponse(400, '');
        }

        try {
            $token = $this->jwt->getToken($data);
            $exp   = $this->jwt->getTTL();

            return $this->jsonResponse(200, '', [
                'token' => (string)$token,
                'exp'   => $exp,
//                'line'  => $data['line_accounts'],
//                'slogan'=>['รับเงินกับเรา', 'รับรายได้รายวัน ส่งงานทุกวัน', 'รับโบนัสเพิ่มอีก 100 บาท ทุกสัปดาห์']
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->jsonResponse(400, $e->getMessage());
        }
    }

    /**
     * 文件上传(素材库)
     * @PostMapping(path="uploads")
     */
    public function uploads(Filesystem $filesystem)
    {
        try {
            $file = $this->request->file('file');
            if (!$file) {
                return $this->jsonResponse(400,'FILE_DOES_NOT_EXIST',[]);
            }
            $size = $file->getSize();
            if ($size / 1024 / 1024 > 10) {
                return $this->jsonResponse(400,'文件大小超过10M',[]);
            }
            $extName = $file->getExtension();

            $dir     = BASE_PATH . '/public/images/';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }



            $fileName = time() . rand(1, 999999);
            $path     = $dir . $fileName . '.' . $extName;

            $file->moveTo($path);

//            @mkdir($dir, 0777, true);
            chmod($dir.$fileName.'.'.$extName, 0755);
            return $this->jsonResponse(200,'',[
                'src' => 'images/'  . $fileName . '.' . $extName
            ]);
        } catch (\Throwable $throwable) {
            return $this->jsonResponse($throwable->getCode(),$throwable->getMessage(),[]);
        }
    }
    /**
     * 图片上传（Task模块）
     * @PostMapping(path="uploadsTask")
     */
    public function uploadsTask(Filesystem $filesystem)
    {
        try {
            $file = $this->request->file('file');
            if (!$file) {
                return $this->jsonResponse(400,'FILE_DOES_NOT_EXIST',[]);
            }
            $size = $file->getSize();
            if ($size / 1024 / 1024 > 10) {
                return $this->jsonResponse(400,'文件大小超过10M',[]);
            }
            $extName = strtolower($file->getExtension());

            $dir     = BASE_PATH . '/public/task/';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $fileName = time() . rand(1, 999999);
            $path     = $dir . $fileName . '.' . $extName;

            $file->moveTo($path);

            chmod($dir.$fileName.'.'.$extName, 0755);
            return $this->jsonResponse(200,'',[
                'src' => 'task/'  . $fileName . '.' . $extName
            ]);

        } catch (\Throwable $throwable) {
            return $this->jsonResponse($throwable->getCode(),$throwable->getMessage(),[]);
        }

    }
}