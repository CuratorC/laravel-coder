<?php

namespace CuratorC\Coder;

use CuratorC\Coder\CoderRequest;
use CuratorC\Coder\Traits\Migrate;
use CuratorC\Coder\Traits\FileOperation;
use CuratorC\Coder\Traits\Controller;
use CuratorC\Coder\Traits\Model;
use CuratorC\Coder\Traits\Blade;
use Carbon\Carbon;
use CuratorC\Coder\Traits\Test;


class Index
{
    use FileOperation; // 文件操作
    use Migrate; // 数据库
    use Controller; // 控制器
    use Model; // 模型
    use Blade; // 页面
    use Test; // 测试

    /**
     *     ';
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
     */

    // 返回数据
    public $result = [];
    // 创建参数
    public $param;
    // 表格字段
    public $tableCol;
    // 模型名称
    public $Model, $Models, $model, $models, $smallModel, $smallModels;
    // 当前日期
    public $today;
    // 默认追加字段
    public $defaultCols;

    // 外键数组
    public $foreignKeyArray = [], $foreignKeyArrayBig = [];

    /**
     * 初始页面
     * @return
     * @author ' . config('coder.file_title.author_name') . '
     * @date(2020-01-26)
     */
    public function index()
    {
        if ($this->initProject()){ // 项目初始化判断
            dd('已解压初始文件，请运行 composer update 更新插件');
        }
        return view('coder::index');
    }

    public function store(CoderRequest $request)
    {
        $this->path($request->all());
        return view('coder::index', ['message' => $this->result]);
    }

    public function path($param)
    {
        // 生成基本数据
        $this->param = $param;
        $this->tableCol = $param['table_col'];
        // 生成6种名称
        $this->Model = $param['model_name'];
        $this->model = $this->createUnderScore($this->Model);
        $this->models = $this->pluralize($this->model);
        $this->Models = $this->createBigHump($this->models);
        $this->smallModel = lcfirst($this->Model);
        $this->smallModels = lcfirst($this->Models);

        // 今天日期
        $this->today = Carbon::now()->toDateString();


        // 默认追加字段
        $this->defaultCols = config('coder.default_cols')??array();


        // 外键数组
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'foreign':
                    $this->foreignKeyArray[] = $col['key'];
                    $this->foreignKeyArrayBig[] = $this->pluralize($col['key']);
                    break;
                default:
            }
        }

        // 数据库文件
        if (isset($param['able_file']['migrate']) && $param['able_file']['migrate'] == 'on') {
            $this->migratePath();
        }

        // 控制器文件
        if (isset($param['able_file']['controller']) && $param['able_file']['controller'] == 'on') {
            $this->controllerPath();
        }
        // 模型文件
        if (isset($param['able_file']['model']) && $param['able_file']['model'] == 'on') {
            $this->modelPath();
        }

        // 视图文件
        if (isset($param['able_file']['blade']) && $param['able_file']['blade'] == 'on') {
            $this->bladePath();
        }

        // 测试文件
        if (isset($param['able_file']['test']) && $param['able_file']['test'] == 'on') {
            $this->testPath();
        }

        $this->addMessage('记得在项目根目录运行 git 指令', 'blue');
        return $this->result;
    }


    /**
     * 添加消息
     * @param            string $message [description]
     * @param            string $type    [description]
     * @author ' . config('coder.file_title.author_name') . '
     * @date(2020-01-27)
     */
    public function addMessage($message = '基础文件创建成功', $type = 'green')
    {
        $this->result[] = ['type'=>$type, 'message'=>$message];
    }

}