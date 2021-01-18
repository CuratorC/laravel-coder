<?php
namespace CuratorC\Coder\Traits;

trait Model
{
    public function modelPath()
    {
        // 创建必要目录
        $this->createDir(app_path().'/Observers');
        $this->createDir(app_path().'/Models');

        $this->createModelContent(); // 模型文件
        $this->createObserverContent(); // 观察者文件

        if ($this->param['table_type'] == 'main' && isset($param['able_file']['blade']) && $param['able_file']['blade'] == 'on')
            $this->createRoleEditViewContent(); // 权限编辑静态页面
    }

    public function createModelContent()
    {
        $text = '<?php

namespace App\Models;
';

        // 若为衍生表
        if ($this->param['table_type'] == 'derive') {
            $text .= '

use App\Models\Traits\CacheList;';
        }

        $text .= '


class ' . $this->Model . ' extends Model
{
';
        // 若为衍生表
        if ($this->param['table_type'] == 'derive') {
            $text .= '

    use CacheList; // 缓存衍生表列表';
        }
        $text .= '

    protected $fillable = [';
        foreach ($this->tableCol as $col) {
            if ($col['type'] == 'foreign') {
                $text .= '\'' . $col['key'] . '_id\', ';
            } else {
                $text .= '\'' . $col['key'] . '\', ';
            }
        }
        $text .= '];

';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'foreign':
                $text .= '
    // ' . $col['name'] . '
    public function ' . $col['key'] . '()
    {
        return $this->belongsTo(' . $this->createBigHump($col['key']) . '::class)->withTrashed();
    }
    ';

                break;
                default:
            }
        }
        $text .= '

    // 获取列表
    public function getList($param)
    {
        return $this->withWhere($param)->coderOrder($param)';

        if (sizeof($this->foreignKeyArray)) {
            $text .= '->with(\'' . implode('\', \'', $this->foreignKeyArray) . '\')';
        }

        $text .= '->coderPaginate($param);
    }

    /**
     * @description TODO: where 条件
     * @param $query
     * @param $param
     * @return mixed
     * @author ' . config('coder.file_title.author_name') . '
     * @date ' . $this->today . '
     */

    public function scopeWithWhere($query, $param)
    {
        ';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', $param->' . $col['key'] . ');
        ';
                break;
                case 'foreign':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . '_id) $query->where(\'' . $col['key'] . '_id\', $param->' . $col['key'] . '_id);
        if ($param->' . $col['key'] . '_name) $query->whereHas(\'' . $col['key'] . '\', function ($query) use ($param) {
            $query->where(\'name\', \'like\', \'%\' . $param->' . $col['key'] . '_name . \'%\');
        });
        ';

                break;
                case 'integer-index':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', $param->' . $col['key'] . ');
        ';
                break;
                case 'integer-unique':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', $param->' . $col['key'] . ');
        ';
                break;
                case 'string':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', \'like\', \'%\' . $param->' . $col['key'] . ' . \'%\');
        ';
                break;
                case 'string-index':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', \'like\', \'%\' . $param->' . $col['key'] . '\'%\');
        ';
                break;
                case 'string-unique':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', \'like\', \'%\' . $param->' . $col['key'] . '\'%\');
        ';
                break;
                case 'tinyInteger':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', $param->' . $col['key'] . ');
        ';
                break;
                case 'text':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', \'like\', \'%\' . $param->' . $col['key'] . ' . \'%\');
        ';
                break;
                case 'image':

                break;
                case 'decimal':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', $param->' . $col['key'] . ');
        ';
                break;
                case 'timestamp':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->coderWhereDate(\'' . $col['key'] . '\', $param->' . $col['key'] . ');
        ';
                break;
                case 'primary':
                $text .= '
        // ' . $col['name'] . '
        if ($param->' . $col['key'] . ') $query->where(\'' . $col['key'] . '\', $param->' . $col['key'] . ');
        ';
                break;
                default:
            }
        }
        $text .= '

        return $query;
    }

}
';
        $this->writeToFile(
            app_path() . '/Models//' . $this->Model . '.php',
            $text
        );
    }


    private function createObserverContent()
    {
        $text = '<?php

namespace App\Observers;

use App\Models\\' . $this->Model . ';
use Auth;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ' . $this->Model . 'Observer
{
    public function created(' . $this->Model . ' $' . $this->model . ')
    {

    }

    public function updating(' . $this->Model . ' $' . $this->model . ')
    {

    }

    public function saving(' . $this->Model . ' $' . $this->model . ')
    {
        $' . $this->model . '->updated_by = Auth::user()->id;';

        // 若为衍生表
        if ($this->param['table_type'] == 'derive') {
            $text .= '
        // 遗忘缓存
        (new ' . $this->Model . '())->forgetCacheList();';
        }
        $text .= '
    }
}
';
        $this->writeToFile(
            app_path() . '/Observers//' . $this->Model . 'Observer.php',
            $text
        );

        // 注册
        $this->registerFile(
            app_path() . '/Providers/ObserverProvider.php',
            '
        // ' . $this->param['object_name'] . '
        \App\Models\\' . $this->Model . '::observe(\App\Observers\\' . $this->Model . 'Observer::class);
        ',
        '//<--观察者注册标记，请勿修改'
        );
    }

    private function createRoleEditViewContent()
    {
        $text = '
                                    <div class="layui-col-md6 layui-col-sm6">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="' . $this->param['object_name'] . '">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看' . $this->param['object_name'] . '信息，' . $this->param['object_name'] . '列表"
                                                           name="' . $this->param['object_name'] . '-查看权限" title="查看"
                                                        {{ in_array(\'' . $this->param['object_name'] . '-查看权限\', $permissions)?\'checked\':\'\' }}>
                                                    <input type="checkbox" tips="新增' . $this->param['object_name'] . '数据"
                                                           name="' . $this->param['object_name'] . '-新增权限" title="新增"
                                                        {{ in_array(\'' . $this->param['object_name'] . '-新增权限\', $permissions)?\'checked\':\'\' }}>
                                                    <input type="checkbox" tips="修改' . $this->param['object_name'] . '资料"
                                                           name="' . $this->param['object_name'] . '-修改权限" title="修改"
                                                        {{ in_array(\'' . $this->param['object_name'] . '-修改权限\', $permissions)?\'checked\':\'\' }}>
                                                    <input type="checkbox" tips="删除' . $this->param['object_name'] . '"
                                                           name="' . $this->param['object_name'] . '-删除权限" title="删除"
                                                        {{ in_array(\'' . $this->param['object_name'] . '-删除权限\', $permissions)?\'checked\':\'\' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    ';
        // 写入
        $this->registerFile(
            app_path() . '/../resources/views/roles/create_and_edit.blade.php',
            $text,
            '{{-- 权限编辑标记，请勿删除 --}}'
        );
    }

}