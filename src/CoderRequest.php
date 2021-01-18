<?php

namespace CuratorC\Coder;

use App\Http\Requests\FormRequest;
class CoderRequest extends FormRequest
{
        public function rules()
    {
        return [
            // CREATE ROLES
            'object_name'       => 'required|string',
            'model_name'        => 'required|string',
            'table_type'        => 'required|string',
            'table_col.*.name'  => 'required|string',
            'table_col.*.key'   => 'required|string',
            'table_col.*.type'  => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'object_name'       => '对象名称',
            'model_name'        => '模型名称',
            'table_col.*.name'  => '字段名',
            'table_col.*.key'   => '键名',
            'table_col.*.width' => '宽度',
            'table_col.*.type'  => '字段类型',
        ];
    }
}