<?php


namespace App\Controller\Home;

use App\Model\Content;
use App\Model\Fontstyle;
use App\Model\Image;
use App\Model\Type;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController(prefix="api")
 */
class ChannelController extends AbstractController
{

    /**
     * 图片查询字段
     * @var array
     */
    protected $image_select = ['id', 'type_id', 'language_id', 'platform_id', 'size_id', 'name', 'enable'];

    /**字体样式查询字段
     * @var array
     */
    protected $fonts_select = ['id', 'image_id', 'font', 'font_color', 'font_size', 'font_position', 'font_shadow', 'font_stroke',
        'textShadow', 'background', 'width', 'textAlign', 'font_rotate', 'borderRadius', 'padding'];

    /**文字内容查询字段
     * @var array
     */
    protected $content_select = ['id', 'type_id', 'language_id', 'content'];


    /**
     * 获取渠道文字内容
     * @PostMapping(path="getContentList")
     */
    public function getContentList(): ResponseInterface
    {
        $strip   = $this->request->input('strip', 1);
        $type_id = $this->request->input('type_id', '');
        $data    = [];
        if ($type_id) {
            $type = Type::query()->where('status', '=', 1)->where('id', '=', $type_id)->get('id')->toArray();
            if (empty($type)) {
                return $this->jsonResponse(400, '类型不可用', []);
            }
            $data = Content::query()->where('type_id', $type_id)
                ->where('language_id', "=", $this->langKey())
                ->select('id', 'type_id', 'language_id', 'content')
                ->inRandomOrder()->take($strip)->get()->map(function ($item) {
                    $item->language = config('app.lang')[$item->language_id];
                    return $item;
                })->toArray();
//            if (!$data) {
//                $data = Content::query()->inRandomOrder()->take(1)->get()->toArray();
//            }
        }
        return $this->jsonResponse(200, '', $data);

    }

    /**
     * 获取渠道图片列表
     * @PostMapping(path="getImageList")
     */
    public function getImageList(): ResponseInterface
    {
        $type_id = $this->request->input('type_id');

        if ($type_id) {
            $type = Type::query()->select('id', 'name', 'domain', 'config', 'foreign', 'status')->where('id', '=', $type_id)->where('status', '=', 1)->get()->map(function ($item) {
                $item->config = json_decode($item->config, true) ?? null;
                return $item;
            });
            if (empty($type->toArray() ?? [])) {
                return $this->jsonResponse(400, '类型不可用', []);
            }
            $image = Image::query()->select($this->image_select)->whereRaw("JSON_SEARCH(`type_id`, 'one', '$type_id')")->inRandomOrder()->take(1)->get();
            $list  = [];
            if ($image) {
                foreach ($image as $value)

                    if ($value->size_id) {
                        $size = explode('x', str_replace(" ", '', config('app.size')[$value->size_id])) ?? null;
                    } else {
                        $size = explode('x', str_replace(" ", '', config('app.size')[1]));
                    }
                $fonts = Fontstyle::query()->select($this->fonts_select)->where('image_id', $value->id)->get()->map(function ($item) {
                    $font_position   = explode('|', $item->font_position) ?? [0, 0];
                    $item->font_left = $font_position[0] ?? null;
                    $item->font_top  = $font_position[1] ?? null;
                    $item->font      = config('app.font')[$item->font] ?? null;

                    return $item;
                });
                $list  = [
                    'name'     => $value->name,
                    'language' => config('app.lang')[$value->language_id] ?? null,
                    'size'     => $size,
                    'fonts'    => $fonts,
                    'type'     => $type[0] ?? []
                ];
            }
            return $this->jsonResponse(200, '', $list);
        }

        return $this->jsonResponse(200, '', []);
    }


    /**
     * @PostMapping(path="getImageLists")
     * @return array|false|string
     */
    public function getImageLists()
    {
        $type_id = $this->request->input('type_id');

        $strip = $this->request->input('strip', 1);

        $Type_id_arr = explode(",", $type_id);
        if ($type_id) {
            $type = Type::query()->whereIn('id', $Type_id_arr)->where('status', '=', 1)->get()->map(function ($item) {
                $item->config = json_decode($item->config, true) ?? null;
                return $item;
            });
            $list = [];
            foreach ($type as $va) {
                $image = Image::query()->select($this->image_select)->whereRaw("JSON_SEARCH(`type_id`, 'one', '$va->id')")->inRandomOrder()->take($strip)->get();
                if ($image) {
                    foreach ($image as $value) {
//                        $font_position = explode('|',$value->font_position) ?? [0,0];
                        if ($value->size_id) {
                            $size = explode('x', str_replace(" ", '', config('app.size')[$value->size_id])) ?? null;
                        } else {
                            $size = explode('x', str_replace(" ", '', config('app.size')[1]));
                        }
                        $fonts  = Fontstyle::query()->select($this->fonts_select)->where('image_id', $value->id)->get()->map(function ($item) {
                            $font_position   = explode('|', $item->font_position) ?? [0, 0];
                            $item->font_left = $font_position[0] ?? null;
                            $item->font_top  = $font_position[1] ?? null;
                            $item->font      = config('app.font')[$item->font] ?? null;

                            return $item;
                        });
                        $list[] = [
                            'name'     => $value->name,
                            'language' => config('app.lang')[$value->language_id] ?? null,
                            'size'     => $size,
                            'fonts'    => $fonts,
                            'type'     => $va ?? []
                        ];
                    }

                }
            }


            return $this->jsonResponse(200, '', $list);
        }


        return $this->jsonResponse(200, '', []);

    }

    /**获取内容（多张）
     * @PostMapping(path="getContentLists")
     * @return array|false|string
     */
    public function getContentLists()
    {
        $type_id = $this->request->input('type_id');

        $strip = $this->request->input('strip', 1);

        $Type_id_arr = explode(",", $type_id);
        if (!empty($Type_id_arr)) {
            $type = Type::query()->whereIn('id', $Type_id_arr)->where('status', '=', 1)
                ->get('id');
            if (empty($type->toArray() ?? [])) {
                return $this->jsonResponse(400, '类型不可用', []);
            }
            $type_ids = [];
            foreach ($type as $value) {
                $type_ids[] = $value->id;
            }
            $content = Content::query()->select('type_id', 'content')->where('language_id', "=", $this->langKey())
                ->whereIn("type_id", $type_ids)->inRandomOrder()->take($strip)->first();

            return $this->jsonResponse(200, '', $content ? $content->toArray() : []);
        }

        return $this->jsonResponse(202, '', []);

    }


    /**
     * 获取单个类型下的所有图片
     * @PostMapping(path="getAllImageList")
     * @return ResponseInterface
     */
    public function getAllImageList(): ResponseInterface
    {
        $type_id = $this->request->input('type_id');

        if ($type_id) {
            $type = Type::query()->where('status', '=', 1)->where('id', '=', $type_id)->get('id')->toArray();
            if (empty($type)) {
                return $this->jsonResponse(400, '类型不可用', []);
            }
            $image = Image::query()->select($this->image_select)->whereRaw("JSON_SEARCH(`type_id`, 'one', '$type_id')")->get()->map(function ($item) {

                $fonts = Fontstyle::query()->select($this->fonts_select)->where('image_id', $item->id)->get()->map(function ($item) {
                    $font_position   = explode('|', $item->font_position) ?? [0, 0];
                    $item->font_left = $font_position[0] ?? null;
                    $item->font_top  = $font_position[1] ?? null;
                    $item->font      = config('app.font')[$item->font] ?? null;
                    return $item;
                });

                $item->fonts = $fonts->toArray() ?? [];
                return $item;
            });

            return $this->jsonResponse(200, '', $image->toArray() ?? []);
        }

        return $this->jsonResponse(202, '', []);
    }


    /**
     * 获取单个类型下的所有文字内容
     * @PostMapping(path="getAllContentList")
     * @return ResponseInterface
     */
    public function getAllContentList(): ResponseInterface
    {
        $type_id = $this->request->input('type_id');
        if ($type_id) {
            $type = Type::query()->where('status', '=', 1)
                ->where('id', '=', $type_id)
                ->get('id')->toArray();
            if (!empty($type)) {
                $content = Content::query()->select($this->content_select)->where('language_id', "=", $this->langKey())->where('type_id', '=', $type_id)->get();

                return $this->jsonResponse(200, '', $content ? $content->toArray() : []);
            }
            return $this->jsonResponse(400, '类型不可用', []);

        }
        return $this->jsonResponse(200, '', []);
    }


    /**
     * @PostMapping (path="getAllImageByTypes")
     */
    public function getAllImageByTypes(): ResponseInterface
    {
        $type_ids = $this->request->input("type_ids");

        if (!$type_ids) {
            return $this->jsonResponse(400);
        }
        $typeId = explode(",", $type_ids);
        $list   = [];
        foreach ($typeId as $key => $value) {
            $imageList = Image::query()->whereRaw("JSON_SEARCH(`type_id`, 'one', '$value')")
                ->select("id", "type_id", "name", "size_id")
                ->get()->map(function ($item) use ($value) {
                    $item->type = [
                        "type_id" => $value,
                        "domain"  => "https://d3p80o5v88i7c7.cloudfront.net/ap/",
                    ];
                    return $item;
                });
            $lists     = $imageList->isNotEmpty() ? $imageList->toArray() : [];
            foreach ($lists as $item) {
                array_push($list, $item);
            }
        }
        return $this->jsonResponse(200, '', $list);
    }
}