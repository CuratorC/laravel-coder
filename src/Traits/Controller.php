<?php
namespace CuratorC\Coder\Traits;

trait Controller
{

    public function controllerPath()
    {
        // 创建必要目录
        $this->createDir(app_path().'/Http/Controllers/Admin');
        $this->createDir(app_path().'/Http/Controllers/Api');
        $this->createDir(app_path().'/Http/Controllers/Api');
        $this->createDir(app_path().'/Http/Requests/Admin');
        $this->createDir(app_path().'/Http/Requests/Api');
        $this->createDir(app_path().'/Http/Requests/Api');
        $this->createDir(app_path().'/Http/Resources');
        $this->createDir(app_path().'/Http/Resources/Admin');
        $this->createDir(app_path().'/Policies');

        // 创建控制器
        if (isset($param['able_file']['blade']) && $param['able_file']['blade'] == 'on') {
            $this->createAdminControllerContent();
            $this->createAdminRequestContent();
            // 路由
            $this->createRouteContent();

        }
        $this->createApiControllerContent();

        // 参数验证
        $this->createApiRequestContent();

        // 数据资源格式
        $this->createResourceContent();
        $this->createCollectionContent();

        // 权限控制文件
        $this->createPoliciesContent();

    }


    private function createAdminControllerContent()
    {
        $text = '<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\\' . $this->Model . 'Request;
use App\Http\Resources\Admin\\' . $this->Models . 'Collection;
use App\Models\\' . $this->Model . ';';

        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'foreign':
                    $text .= '
use App\Models\\' . $this->createBigHump($col['key']) . ';';

                    break;
                default:
            }
        }

        $text .= '

class ' . $this->Models . 'Controller extends Controller
{
    public function __construct()
    {
        $this->middleware(\'auth\');
    }

    /**
     * @description TODO: ' . $this->param['object_name'] . '列表静态页面
     * @param ' . $this->Model . '   $' . $this->model . ' ' . $this->param['object_name'] . '模型
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function index(' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'show\', $' . $this->model . ');
        ';

        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'foreign':
                    $text .= '
        // ' . $col['name'] . '
        $' . $this->pluralize($col['key']) . ' = ' . $this->createBigHump($col['key']) . '::all();
                    ';

                    break;
                default:
            }
        }

        if (sizeof($this->foreignKeyArray)) {
            $text .= '
        return view(\'' . $this->models . '.index\', compact(\'';

            $text .= implode('\', \'', $this->foreignKeyArrayBig);

            $text .= '\'));';
        } else {
            $text .= '
        return view(\'' . $this->models . '.index\');';
        }






        $text .= '
    }

    /**
     * @description TODO: ' . $this->param['object_name'] . '列表数据
     * @param ' . $this->Model . 'Request $request
     * @param ' . $this->Model . '        $' . $this->model . '
     * @return ' . $this->Models . 'Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function list(' . $this->Model . 'Request $request, ' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'show\', $' . $this->model . ');

        return new ' . $this->Models . 'Collection($' . $this->model . '->getList($request));
    }

    /**
     * @description TODO: 修改' . $this->param['object_name'] . '状态
     * @param ' . $this->Model . 'Request $request
     * @param ' . $this->Model . ' $' . $this->model . '
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function status(' . $this->Model . 'Request $request, ' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'update\', $' . $this->model . ');

        $' . $this->model . '->status = $request->status;
        $' . $this->model . '->save();

        return successResult();
    }

    /**
     * @description TODO: ' . $this->param['object_name'] . '信息备注
     * @param ' . $this->Model . 'Request $request
     * @param ' . $this->Model . '        $' . $this->model . '
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function remark(' . $this->Model . 'Request $request, ' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'show\', $' . $this->model . ');

        $' . $this->model . '->admin_remark = $request->admin_remark;
        $' . $this->model . '->save();

        return successResult();
    }

    /**
     * @description TODO: 展示' . $this->param['object_name'] . '静态页面
     * @param ' . $this->Model . '   $' . $this->model . '
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function show(' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'show\', $' . $this->model . ');

        return view(\'' . $this->models . '.show\', compact(\'' . $this->model . '\'));
    }

    /**
     * @description TODO: 创建' . $this->param['object_name'] . '静态页面
     * @param ' . $this->Model . '   $' . $this->model . '
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function create(' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'add\', $' . $this->model . ');
        ';

        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'foreign':
                    $text .= '
        // ' . $col['name'] . '
        $' . $this->pluralize($col['key']) . ' = ' . $this->createBigHump($col['key']) . '::all();
                    ';

                    break;
                default:
            }
        }

        if (sizeof($this->foreignKeyArray)) {
            $text .= '
        return view(\'' . $this->models . '.create_and_edit\', compact(\'' . $this->model . '\', \'';

            $text .= implode('\', \'', $this->foreignKeyArrayBig);

            $text .= '\'));';
        } else {
            $text .= '
        return view(\'' . $this->models . '.create_and_edit\', compact(\'' . $this->model . '\'));';
        }


        $text .= '

    }

    /**
     * @description TODO: 创建' . $this->param['object_name'] . '
     * @param ' . $this->Model . 'Request $request
     * @param ' . $this->Model . '        $' . $this->model . '
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function store(' . $this->Model . 'Request $request, ' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'add\', $' . $this->model . ');

        // 创建' . $this->param['object_name'] . '
        $' . $this->model . ' = $' . $this->model . '->create(array_filter($request->all()));

        return closeLayer(\'新增成功\');
    }

    /**
     * @description TODO: 编辑客户静态页面
     * @param ' . $this->Model . '   $' . $this->model . '
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function edit(' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'update\', $' . $this->model . ');';

        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'foreign':
                    $text .= '
        // ' . $col['name'] . '
        $' . $this->pluralize($col['key']) . ' = ' . $this->createBigHump($col['key']) . '::all();
                    ';

                    break;
                default:
            }
        }

        if (sizeof($this->foreignKeyArray)) {
            $text .= '
        return view(\'' . $this->models . '.create_and_edit\', compact(\'' . $this->model . '\', \'';

            $text .= implode('\', \'', $this->foreignKeyArrayBig);

            $text .= '\'));';
        } else {
            $text .= '
        return view(\'' . $this->models . '.create_and_edit\', compact(\'' . $this->model . '\'));';
        }

        $text .= '
    }

    /**
     * @description TODO: 编辑' . $this->param['object_name'] . '
     * @param ' . $this->Model . 'Request $request
     * @param ' . $this->Model . '        $' . $this->model . '
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author: Curator
     * @date ' . $this->today . '
     */
    public function update(' . $this->Model . 'Request $request, ' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'update\', $' . $this->model . ');

        $' . $this->model . '->update($request->all());

        return closeLayer(\'修改成功\');
    }

    /**
     * @description TODO: 删除' . $this->param['object_name'] . '
     * @param ' . $this->Model . 'Request $request
     * @param ' . $this->Model . '        $' . $this->model . '
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @author: Curator
     * @date ' . $this->today . '
     */
    public function destroy(' . $this->Model . 'Request $request, ' . $this->Model . ' $' . $this->model . ')
    {
        $this->authorize(\'destroy\', $' . $this->model . ');

        $' . $this->model . '->delete();

        return successResult(\'删除成功\');
    }
}
';
        $this->writeToFile(
            app_path() . '/Http/Controllers/Admin/' . $this->Models . 'Controller.php',
            $text
        );
    }


    private function createApiControllerContent()
    {
        $text = '<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\\' . $this->Model . 'Request;
use App\Http\Resources\\' . $this->Model . 'Resource;
use App\Models\\' . $this->Model . ';

class ' . $this->Models . 'Controller extends Controller
{

    /**
     * @description TODO: ' . $this->param['object_name'] . '列表
     * @param ' . $this->Model . 'Request $request
     * @param ' . $this->Model . '        $' . $this->model . '
     * @return mixed
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */
    public function index(' . $this->Model . 'Request $request, ' . $this->Model . ' $' . $this->model . ')
    {
        $' . $this->models . ' = $' . $this->model . '->getList($request);
        return ' . $this->Model . 'Resource::collection($' . $this->models . ');
    }

    /**
     * @description TODO: 展示' . $this->param['object_name'] . '静态页面
     * @param ' . $this->Model . '        $' . $this->model . '
     * @return ' . $this->Model . 'Resource
     * @author: Curator
     * @date ' . $this->today . '
     */
    public function show(' . $this->Model . ' $' . $this->model . ')
    {
        return new ' . $this->Model . 'Resource($' . $this->model . ');
    }

}
';
        $this->writeToFile(
            app_path() . '/Http/Controllers/Api/' . $this->Models . 'Controller.php',
            $text
        );
    }


    private function createAdminRequestContent()
    {
        $text = '<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
class ' . $this->Model . 'Request extends Request
{
    public function rules()
    {

        switch (request()->route()->getActionMethod()) {
            case \'store\':
                return [';
            foreach ($this->tableCol as $col) {
                switch ($col['type']) {
                    case 'integer':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'nullable|integer\',';
                    break;
                    case 'foreign':
                    $text .= '
                    \'' . $col['key'] . '_id\'     => \'required|integer|exists:' . $this->pluralize($col['key']) . ',id\',';

                    break;
                    case 'integer-index':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'required|integer\',';

                    break;
                    case 'integer-unique':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'required|integer|unique:' . $this->models . ',' . $col['key'] . '\',';

                    break;
                    case 'string':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'nullable|string\',';

                    break;
                    case 'string-index':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'required|string\',';

                    break;
                    case 'string-unique':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'required|string|exists:' . $this->models . ',' . $col['key'] . '\',';

                    break;
                    case 'tinyInteger':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'nullable|integer|between:1,9\',';

                    break;
                    case 'text':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'nullable|string\',';

                    break;
                    case 'image':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'nullable|string\',';

                    break;
                    case 'decimal':

                    break;
                    case 'timestamp':
                    $text .= '
                    \'' . $col['key'] . '\'     => \'nullable|date\',';

                    break;
                    case 'primary':

                    break;
                    default:
                }
            }
            $text .= '
                ];
            break;
            case \'show\':
                return [
                    \'id\'     => \'required|integer|exists:' . $this->models . ',id\',
                ];
            default:
            return [];
        }
    }

    public function messages()
    {
        return [
            \'example.demo\'            => \'自定义返回信息示例\',
        ];
    }

    public function attributes()
    {
        return [';
        foreach ($this->tableCol as $col) {
            // 外键判断
            if ($col['type'] == 'foreign') {
                $text .= '
            \'' . $col['key'] . '_id\'         => \'' . $col['name'] . '\',';
            } else {
                $text .= '
            \'' . $col['key'] . '\'         => \'' . $col['name'] . '\',';
            }

        }
        $text .= '
        ];
    }
}
';
        $this->writeToFile(
            app_path() . '/Http/Requests/Admin/' . $this->Model . 'Request.php',
            $text
        );
    }


    private function createApiRequestContent()
    {
        $text = '<?php

namespace App\Http\Requests\Api;

class ' . $this->Model . 'Request extends FormRequest
{
    public function rules()
    {

        switch (request()->route()->getActionMethod()) {
            case \'store\':
            return [';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
                \'' . $col['key'] . '\'     => \'nullable|integer\',';
                break;
                case 'foreign':
                $text .= '
                \'' . $col['key'] . '_id\'     => \'required|integer|exists:' . $this->pluralize($col['key']) . ',id\',';

                break;
                case 'integer-index':
                $text .= '
                \'' . $col['key'] . '\'     => \'required|integer\',';

                break;
                case 'integer-unique':
                $text .= '
                \'' . $col['key'] . '\'     => \'required|integer|unique:' . $this->models . ',' . $col['key'] . '\',';

                break;
                case 'string':
                $text .= '
                \'' . $col['key'] . '\'     => \'nullable|string\',';

                break;
                case 'string-index':
                $text .= '
                \'' . $col['key'] . '\'     => \'required|string\',';

                break;
                case 'string-unique':
                $text .= '
                \'' . $col['key'] . '\'     => \'required|string|exists:' . $this->models . ',' . $col['key'] . '\',';

                break;
                case 'tinyInteger':
                $text .= '
                \'' . $col['key'] . '\'     => \'nullable|integer|between:1,9\',';

                break;
                case 'text':
                $text .= '
                \'' . $col['key'] . '\'     => \'nullable|string\',';

                break;
                case 'image':
                $text .= '
                \'' . $col['key'] . '\'     => \'nullable|string\',';

                break;
                case 'decimal':

                break;
                case 'timestamp':
                $text .= '
                \'' . $col['key'] . '\'     => \'nullable|date\',';

                break;
                case 'primary':

                break;
                default:
            }
        }
        $text .= '
            ];
            break;
            case \'show\':
            return [
                \'id\'     => \'required|integer|exists:' . $this->models . ',id\',
            ];
            default:
            return [];
        }
    }

    public function messages()
    {
        return [
            \'example.demo\'            => \'自定义返回信息示例\',
        ];
    }

    public function attributes()
    {
        return [';
        foreach ($this->tableCol as $col) {
            // 外键判断
            if ($col['type'] == 'foreign') {
                $text .= '
            \'' . $col['key'] . '_id\'         => \'' . $col['name'] . '\',';
            } else {
                $text .= '
            \'' . $col['key'] . '\'         => \'' . $col['name'] . '\',';
            }

        }

        if (in_array('status', $this->defaultCols)) {
            $text .= '
            \'status\'         => \'状态\',';
        }

        $text .= '
        ];
    }
}
';
        $this->writeToFile(
            app_path() . '/Http/Requests/Api/' . $this->Model . 'Request.php',
            $text
        );
    }

    private function createResourceContent()
    {
        $text = '<?php

namespace App\Http\Resources;

class ' . $this->Model . 'Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'foreign':
                $text .= '
            \'' . $col['key'] . '\' => new ' . $this->createBigHump($col['key']) . 'Resource($this->whenLoaded(\'' . $col['key'] . '\')),';

                break;
                case 'integer-index':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'integer-unique':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'string':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'string-index':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'string-unique':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'tinyInteger':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',
            \'' . $col['key'] . '_name\' => ' . $this->Model . '::get' . $this->createBigHump($col['key']) . 'Name()[$this->' . $col['key'] . '],';
                break;
                case 'text':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'image':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                case 'decimal':
                $text .= '
            \'' . $col['key'] . '\' => formatMoney($this->' . $col['key'] . '),';

                break;
                case 'timestamp':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . '->toDatetimeString(),
            \'' . $col['key'] . '_diff\' => $this->' . $col['key'] . '->diffForHumans(),';

                break;
                case 'primary':
                $text .= '
            \'' . $col['key'] . '\' => $this->' . $col['key'] . ',';

                break;
                default:
            }
        }

        if (in_array('status', $this->defaultCols)) {
            $text .= '
            \'status\' => $this->status,
            \'status_name\' => ' . $this->Model . '::getStatusName()[$this->status],';
        }
        if (in_array('admin_remark', $this->defaultCols)) {
            $text .= '
            \'admin_remark\' => $this->admin_remark,';
        }
        if (in_array('timestamps', $this->defaultCols)) {
            $text .= '
            \'created_at\' => $this->created_at->toDatetimeString(),
            \'created_at_diff\' => $this->created_at->diffForHumans(),
            \'updated_at\' => $this->created_at->toDatetimeString(),
            \'updated_at_diff\' => $this->updated_at->diffForHumans(),';
        }

        $text .= '
            /*$this->mergeWhen(Auth::user()->isAdmin(), [
            ]),*/
        ];
    }
}
';
        $this->writeToFile(
            app_path() . '/Http/Resources/' . $this->Model . 'Resource.php',
            $text
        );
    }

    private function createCollectionContent()
    {
        $text = '<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\\' . $this->Model . 'Resource;

class ' . $this->Models . 'Collection extends ResourceCollection
{
    public $collects = ' . $this->Model . 'Resource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            \'data\'  => $this->collection,
            \'code\'  => 0,
        ];
    }
}
';
        $this->writeToFile(
            app_path() . '/Http/Resources/Collections/' . $this->Models . 'Collection.php',
            $text
        );
    }

    private function createPoliciesContent()
    {
        $text = '<?php

namespace App\Policies;

use App\Models\User;
use App\Models\\' . $this->Model . ';

class ' . $this->Model . 'Policy extends Policy
{
    public function show(User $currentUser, ' . $this->Model . ' $' . $this->model . ')
    {
        return $currentUser->can(\'';
        if ($this->param['table_type'] == 'main') {
            $text .= $this->param['object_name'] . '-查看权限';
        } else {
            $text .= '管理权限';
        }
        $text .= '\');
    }
    public function add(User $currentUser, ' . $this->Model . ' $' . $this->model . ')
    {
        return $currentUser->can(\'';
        if ($this->param['table_type'] == 'main') {
            $text .= $this->param['object_name'] . '-新增权限';
        } else {
            $text .= '管理权限';
        }
        $text .= '\');
    }
    public function update(User $currentUser, ' . $this->Model . ' $' . $this->model . ')
    {
        return $currentUser->can(\'';
        if ($this->param['table_type'] == 'main') {
            $text .= $this->param['object_name'] . '-修改权限';
        } else {
            $text .= '管理权限';
        }
        $text .= '\');
    }
    public function destroy(User $currentUser, ' . $this->Model . ' $' . $this->model . ')
    {
        return $currentUser->can(\'';
        if ($this->param['table_type'] == 'main') {
            $text .= $this->param['object_name'] . '-删除权限';
        } else {
            $text .= '管理权限';
        }
        $text .= '\');
    }
}
';
        $this->writeToFile(
            app_path() . '/Policies/' . $this->Model . 'Policy.php',
            $text
        );


        // 注册
        $this->registerFile(
            app_path() . '/Providers/AuthServiceProvider.php',
            '
        // ' . $this->param['object_name'] . '
        \App\Models\\' . $this->Model . '::class => \App\Policies\\' . $this->Model . 'Policy::class,
        ',
        '];'
        );
    }

    private function createRouteContent()
    {
        $text = '

    // ' . $this->param['object_name'] . '
    // ' . $this->param['object_name'] . '列表
    Route::get(\'' . $this->models . '/list\', \'' . $this->Models . 'Controller@list\')->name(\'' . $this->models . '.list\');
    // ' . $this->param['object_name'] . '状态
    Route::put(\'' . $this->models . '/{' . $this->model . '}/status/{status}\', \'' . $this->Models . 'Controller@status\')->name(\'' . $this->models . '.status\');
    // ' . $this->param['object_name'] . '备注
    Route::put(\'' . $this->models . '/{' . $this->model . '}/remark\', \'' . $this->Models . 'Controller@remark\')->name(\'' . $this->models . '.remark\');
    // ' . $this->param['object_name'] . '基础路由
    Route::resource(\'' . $this->models . '\', \'' . $this->Models . 'Controller\', [\'index\', \'show\', \'create\', \'store\', \'update\', \'edit\', \'destroy\']);
';

        // 加入web.php
        $this->registerFile(
            app_path() . '/../routes/web.php',
            $text,
            '});'
        );
    }
}