<?php
namespace App\Models\Traits;

use Carbon\Carbon;

trait ScopeCoderSearch
{

    /**
     *ㅤ order 排序
     * @param $query
     * @param $param
     * @param mixed ...$orderRules
     * @date 2020/9/27
     * @author ' . config('coder.file_title.author_name') . '
     */
    public function scopeCoderOrder($query, $param, ...$orderRules)
    {
        // 检查用户自带参数
        $query->when($param->field, function ($query) use ($param) {
            // 补全 order 参数、
            if (empty($param->order)) $param->order = 'ASC';
            $query->orderBy($param->field, $param->order);
        });

        // 循环程序自定义参数
        foreach ($orderRules as $orderRule) {
            if (is_string($orderRule)) $query->orderBy($orderRule);
            if (is_array($orderRule)) $query->orderBy($orderRule[0], $orderRule[1]);
        }

        // 追加固定排序
        $query->orderBy('id', 'DESC');
    }

    /**
     *ㅤ解析 path 查找父级
     * @param $query
     * @param $id
     * @param string $field
     * @date 2020/9/27
     * @author ' . config('coder.file_title.author_name') . '
     */
    public function scopeCoderIdInPath($query, $id, $field = 'path')
    {
        $query->where(function ($query) use ($id, $field) {
            $query->where('id', $id)
                ->orWhere($field, 'like', '%-' . $id . '-%')
                ->orWhere($field, 'like', $id . '-%')
                ->orWhere($field, 'like', '%-' . $id)
                ->orWhere($field, $id);
        });
    }

    /**
     *ㅤ解析 path 查找父级
     * @param $query
     * @param $name
     * @param $modelName
     * @param string $nameField
     * @param string $pathField
     * @date 2020/9/27
     * @author ' . config('coder.file_title.author_name') . '
     */
    public function scopeCoderNameInPath($query, $name, $modelName, $nameField = 'name', $pathField = 'path')
    {
        // 先将 name 转换为 model, 然后根据 model->id 使用 scopeCoderIdInPath
        $models = $modelName::where($nameField, 'like', '%' . $name . '%')->get();
        $query->where(function ($query) use ($models, $pathField) {
            foreach ($models as $model) {
                $query->orWhere(function ($query) use ($model, $pathField) {
                    $query->coderIdInPath($model->id, $pathField);
                });
            }
        });
    }

    /**
     *ㅤ日期筛选
     * @param $query
     * @param $field
     * @param $param
     * @date 2020/9/29
     * @author ' . config('coder.file_title.author_name') . '
     */
    public function scopeCoderWhereDate($query, $field, $param)
    {
        if (preg_match("/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) - [1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $param)) {
            $timeArray = explode(' - ', $param);
            // 结束日期加一天
            $endDay = Carbon::create($timeArray[1])->addDay();
            $query->where($field, '>=', $timeArray[0])->where($field, '<=', $endDay);
        }
    }

    /**
     *ㅤ when 查询
     * @param $query
     * @param $field
     * @param $param
     * @param $func
     * @date 2020/10/12
     * @author ' . config('coder.file_title.author_name') . '
     */
    public function scopeCoderWhen($query, $field, $param, $func)
    {
        $query->when($param->$field, function ($query) use ($func, $param, $field) {
            $func($query, $param->$field);
        });

        // 因内存爆炸，暂时不添加 keyword 查询模式。以后再找是啥问题
        /*->when($param->keyword, function ($query) use ($func, $param, $field) {
            $func($query, $param->$field);
        });*/
    }
    public function scopeCoderWhenKeyword($query, $field, $param, $func)
    {
        $query->when($param->$field, function ($query) use ($func, $param, $field) {
            $func($query, $param->$field);
        })->when($param->keyword, function ($query) use ($func, $param, $field) {
            $func($query, $param->$field);
        });
    }

    /**
     *ㅤ分页
     * @param $query
     * @param $param
     * @return mixed
     * @date 2020/10/13
     * @author ' . config('coder.file_title.author_name') . '
     */
    public function scopeCoderPaginate($query, $param)
    {
        if (empty($param->size)) $param->size = 10;
        return $query->paginate($param->size);
    }
}
