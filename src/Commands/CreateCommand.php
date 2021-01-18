<?php

namespace CuratorC\Coder\Commands;

use Illuminate\Console\Command;
use CuratorC\Coder\Index;

class CreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coder:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建基础文件';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->process();
    }

    private function process()
    {

        // 删除检测
        $logic = new Index();
        if ($logic->initProject()){ // 项目初始化判断
            dd('已解压初始文件，请运行 composer update 更新插件');
        }

        $modelContent = array();
        // 展示必要信息。
        $this->outputMessage('作者姓名： ' . config('coder.file_title.author_name'));
        // 询问创建内容
        if ($this->getInput('是否要创建默认文件？', false, true)) {
            $modelContent['able_file'] = config('coder.able_file', [
                'migrate'   => 'on',
                'controller' => 'on',
                'model' => 'on',
                'test'  => 'on',
            ]);
        } else {
            if ($this->getInput('是否要创建数据库文件？', false, true)) $modelContent['able_file']['migrate'] = 'on';
            if ($this->getInput('是否要创建视图文件？', false, true)) $modelContent['able_file']['blade'] = 'on';
            if ($this->getInput('是否要创建控制器文件？', false, true)) $modelContent['able_file']['controller'] = 'on';
            if ($this->getInput('是否要创建模型文件？', false, true)) $modelContent['able_file']['model'] = 'on';
            if ($this->getInput('是否要创建测试文件？', false, true)) $modelContent['able_file']['test'] = 'on';
        }
        // 录入必要信息。
        $modelContent['object_name'] = $this->getInput('对象名称：[模型中文名称]');
        $modelContent['model_name'] = $this->getInput('模型名称：[大驼峰单数形式]');
        $modelContent['table_type'] = $this->getInput('表格类别：yes: 业务表, no: 衍生表', false, true, false)?'main':'derive';

        $modelContent['table_col'] = $this->addTable();

        $this->outputMessage($logic->path($modelContent));

    }

    /**
     *ㅤ添加表格
     * @param array $tableCol
     * @return array|mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function addTable($tableCol = [])
    {
        // 展示当前录入字段
        $this->showTableCol($tableCol);
        // 操作菜单
        $this->outputMessage('操作菜单  A: add,添加, E: edit,修改, D: delete,删除, S:submit,提交', 'yellow');
        $check = $this->getInput('操作');
        switch ($check) {
            case 'A':
            $tableCol[] = $this->addTableColName();
            break;
            case 'E':
            $tableCol = $this->editTableCol($tableCol);
            break;
            case 'D':
            $tableCol = $this->deleteTableCol($tableCol);
            break;
            case 'S':
            if ($this->getInput('确认提交？提交后将开始创建文件！', false, true, false)) {
                return $tableCol;
            } else {
                return $this->addTable($tableCol);
            }
            default:
            $this->outputMessage('默认执行 A: add,添加', 'yellow');
            $tableCol[] = $this->inputTableColName($check);
            break;
        };

        // 递归
        return $this->addTable($tableCol);

    }

    /**
     *ㅤ新增字段名称
     * @return mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function addTableColName()
    {
        $key = $this->getInput('请输入字段键名或字段信息（用 | 隔开）');
        return $this->inputTableColName($key);
    }


    /**
     *ㅤ录入字段名称
     * @param $key
     * @return mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function inputTableColName($key)
    {
        if (preg_match('/\|/', $key)) {
            $dataArray = explode('|', $key);
            if (isset($dataArray[0])) {
                $col['key'] = $dataArray[0];
                if (isset($dataArray[1])) {
                    $col['name'] = $dataArray[1];
                    if (isset($dataArray[2])) {
                        $col['type'] = $this->insertTableColType($dataArray[2]);
                        return $col;
                    } else {
                        return $this->addTableColType($col);
                    }
                } else {
                    return $this->addTableColKey($col);
                }
            } else {
                $this->outputMessage('添加失败，请重新输入', 'red');
                return $this->addTableColName();
            }
        } else {
            return $this->addTableColKey(['key' => $key]);
        }
    }

    /**
     *ㅤ写入字段键名
     * @param $col
     * @return mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function addTableColKey($col)
    {
        $name = $this->getInput('请输入字段名或字段信息（用 | 隔开）');

        if (preg_match('/\|/', $name)) {
            $dataArray = explode('|', $name);
            if (isset($dataArray[0])) {
                $col['name'] = $dataArray[0];
                if (isset($dataArray[1])) {
                    $col['type'] = $this->insertTableColType($dataArray[1]);
                    return $col;
                } else {
                    return $this->addTableColType($col);
                }
            } else {
                $this->outputMessage('添加失败，请重新输入', 'red');
                return $this->addTableColKey($col);
            }
        } else {
            $col['name'] = $name;
            return $this->addTableColType($col);
        }
    }

    /**
     *ㅤ录入字段类型
     * @param $col
     * @return mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function addTableColType($col)
    {
        $this->outputTableColTypeHint();
        $col['type'] = $this->insertTableColType($this->getInput('请输入字段类型'));
        return $col;
    }

    /**
     *ㅤ录入字段类型 helper
     * @param $type
     * @return mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function insertTableColType($type)
    {
        if (in_array($type, ['integer', 'foreign', 'integer-index',
            'integer-unique', 'string', 'string-index', 'string-unique',
            'tinyInteger', 'integer', 'text', 'image', 'decimal', 'timestamp', 'primary'])) {
            return $type;
        } else {
            $this->outputMessage('字段类型错误，请重新输入字段类型！', 'red');
            $this->outputTableColTypeHint();
            return $this->insertTableColType($this->getInput('请输入字段类型'));
        }
    }

    /**
     *ㅤ修改表格字段
     * @param $tableCol
     * @return mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function editTableCol($tableCol)
    {
        // 获取要删除的 key
        $key = $this->getInsertTableKey($tableCol);
        // 删除 key 值
        $tableCol[$key] = $this->addTableColName();
        return $tableCol;
    }

    /**
     *ㅤ删除表格字段
     * @param $tableCol
     * @return mixed
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function deleteTableCol($tableCol)
    {
        // 获取要删除的 key
        $key = $this->getInsertTableKey($tableCol);
        // 删除 key 值
        unset($tableCol[$key]);
        return $tableCol;
    }

    /**
     *ㅤ输出字段类型提示信息
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function outputTableColTypeHint()
    {
        $this->outputMessage([
            ['type' => 'blue', 'message' => 'integer 数字'],
            ['type' => 'yellow', 'message' => 'foreign 外键'],
            ['type' => 'blue', 'message' => 'integer-index 带索引的数字'],
            ['type' => 'yellow', 'message' => 'integer-unique 带唯一索引的数字'],
            ['type' => 'blue', 'message' => 'string 字符串'],
            ['type' => 'yellow', 'message' => 'string-index 带索引的字符串'],
            ['type' => 'blue', 'message' => 'string-unique 带唯一索引的字符串'],
            ['type' => 'yellow', 'message' => 'tinyInteger 状态值'],
            ['type' => 'blue', 'message' => 'integer 数字'],
            ['type' => 'yellow', 'message' => 'text 富文本'],
            ['type' => 'blue', 'message' => 'image 图片'],
            ['type' => 'yellow', 'message' => 'decimal 金额/小数'],
            ['type' => 'blue', 'message' => 'timestamp 时间'],
            ['type' => 'yellow', 'message' => 'primary 主键'],
        ]);
    }

    /**
     *ㅤ展示当前录入字段
     * @param $tableCol
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function showTableCol($tableCol)
    {
        $this->outputMessage('当前录入字段：');
        $this->outputDecode();
        $this->table(['字段', '名称', '类型'], $tableCol);
    }

    /**
     *ㅤ输出信息
     * @param $messages
     * @param string $type
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function outputMessage($messages, $type = 'green')
    {
        if(is_array($messages)) {
            foreach ($messages as $item) {
                $this->printMessage($item['type'], $item['message']);
            }
        } else {
            $this->printMessage($type, $messages);
        }
    }

    /**
     *ㅤ输入信息
     * @param $type
     * @param $message
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function printMessage($type, $message)
    {
        $this->outputDecode();
        switch ($type) {
            case 'green':
                $this->info($message);
                break;
            case 'yellow':
                $this->comment($message);
                break;
            case 'red':
                $this->error($message);
                break;
            case 'white':
                $this->line($message);
                break;
        }
    }

    /**
     *ㅤ获取输入内容
     * @param string $title     提示信息
     * @param false $nullable   是否允许为空，默认不允许
     * @param false $confirm    是否为判断框， 默认非判断框
     * @param bool $default     判断框默认值，默认为 true
     * @return string           输入内容
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function getInput($title = '', $nullable = false, $confirm = false, $default = true)
    {
        // 判断或输入
        $this->outputDecode();
        if ($confirm) {
            $result = $this->confirm($title, $default);
        } else {
            // 若为 windows 输入，
            if ($this->systemIsWindows()) $result = $this->unicodeDecode($this->ask($title));
            else $result = $this->ask($title);
        }

        // 当输入框为判断时，无需二次判断，直接返回结果
        if($confirm) return $result;

        // 输入值判断
        if ($result == false && $result !== '0') { // 若输入了空
            if ($nullable) { // 输入值允许为空
                if ($this->confirm('您的输入为空，确定要继续么？', true)) {
                    return $result;
                } else {
                    return $this->getInput($title, true);
                }
            } else { // 输入值不许为空
                if ($this->systemIsWindows()) $this->outputMessage('输入内容产生错误！windows 系统不支持输入中文！若要输入中文，首选需要进行一次 unicode 转码，将中文对应的 unicode 编码输入才能正确识别。建议使用 linux 系统进行交互式操作，或者使用静态页面（ http://{{project-address}}/coder ）进行可视化操作。', 'red');
                if ($this->systemIsWindows()) $this->outputMessage('输入内容不允许为空！', 'red');
                return $this->getInput($title);
            }
        } else { // 若输入了内容

            // 若为 windows 系统进行的操作，需要对输入内容再确认一次。
            if ($this->systemIsWindows()) {
                $this->outputMessage('您的输入为：', 'yellow');
                if ($this->confirm($result, true) === true) {
                    return $result;
                } else {
                    return $this->getInput($title, $nullable, $confirm);
                }
            } else return $result;

        }
    }

    /**
     *ㅤ获取输入的表格 key 值
     * @param $tableCol
     * @return array|mixed|string
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function getInsertTableKey($tableCol)
    {
        // 带序号的展示字段信息
        $printArray = array();
        foreach ($tableCol as $key => $item) {
            $printArray[] = [$key, $item['name'] . ', ' . $item['key'] . ', ' . $item['type']];
        }
        $this->table(['序号', '字段'], $printArray);
        $key = $this->getInput('请输入要改动的序号');
        if (isset($tableCol[$key])) {
            return $key;
        } else {
            $this->outputMessage('输入的 key 值不存在，请重新输入！', 'red');
            return $this->addTable($tableCol);
        }
    }

    /**
     *ㅤ判断运行系统是否为 windows
     * @return bool
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function systemIsWindows()
    {
        $systemName = php_uname('s');
        if (strpos($systemName, 'Window') === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *ㅤ输出信息时进行编码判断
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function outputDecode()
    {
        if ($this->systemIsWindows()) exec("CHCP 65001");
    }

    /**
     * unicode 反编译
     * @param $unicode_str
     * @return mixed|string
     * @date 2020/11/5
     * @author ' . config('coder.file_title.author_name') . '
     */
    private function unicodeDecode($unicode_str){
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }
}