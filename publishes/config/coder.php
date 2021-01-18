<?php

return [

    'file_title'    => [
        'author_name'   => '请在配置文件中修改作者姓名' // 用户名，用于注释中的作者标识
    ],

    // 默认追加字段
    'default_cols'   => ['status', 'admin_remark', 'timestamps', 'updated_by', 'deleted_at'],

    // 默认创建文件
    'able_file'     => [
        'migrate'   => 'on',    // 数据库包含 migration, factories, seeders 文件
        // 'blade'     => 'on',    // 页面包含 views 的 index, create_and_edit, show, card 文件
        'controller'=> 'on',    // 控制器包含 Admin 和 Api 的 Controller, Request, Policies 文件
        'model'     => 'on',    // 模型包含 Model, Observers 文件
        'test'      => 'on',    // 测试文件
    ]
];