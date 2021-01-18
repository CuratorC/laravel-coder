<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Code 生成工具</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
    content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{ asset('vendor/curatorc/coder/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('vendor/curatorc/coder/admin.css') }}" media="all">

    <script src="{{ asset('vendor/curatorc/coder/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/curatorc/coder/layui/layui.js') }}"></script>

    {{--toastr提示--}}
    <link href="{{ asset('vendor/curatorc/coder/toastr/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/curatorc/coder/toastr/toastr.min.js') }}"></script>
</head>
<body>

    @if(isset($message))
    @foreach ($message as $item)
    <script>
        @switch($item['type'])
            @case('green')
            toastr.success('{{ $item['message'] }}');
            @break
            @case('yellow')
            toastr.warning('{{ $item['message'] }}');
            @break
            @case('red')
            toastr.error('{{ $item['message'] }}');
            @break
            @case('blue')
            toastr.info('{{ $item['message'] }}');
            @break
            @default
        @endswitch
    </script>
    @endforeach
    @endif

    <div class="layui-fluid">
        <form class="layui-form" method="POST">
            @csrf
            <div class="layui-card">
                <div class="layui-card-header">Code 生成工具</div>
            </div>
            <div class="layui-card">
                <div class="layui-card-header">基本信息</div>
                <div class="layui-card-body" style="padding: 15px;">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">作者姓名</label>
                            <div class="layui-input-block">
                                <input type="text" value="{{ config('coder.file_title.author_name') }}" disabled class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">生成文件</label>
                        <div class="layui-input-block">
                            <input type="checkbox" tips="数据库包含 migration, factories, seeders 文件" name="able_file[migrate]"
                            title="数据库" checked>
                            <input type="checkbox" tips="页面包含 views 的 index, create_and_edit, show, card 文件"
                            name="able_file[blade]" title="页面" checked>
                            <input type="checkbox" tips="测试文件包含单元测试与模块测试"
                            name="able_file[test]" title="测试" checked>
                            <input type="checkbox" tips="控制器包含 Admin 和 Api 的 Controller, Request, Policies 文件"
                            name="able_file[controller]" title="控制器" checked>
                            <input type="checkbox" tips="模型包含 Model, Observers 文件" name="able_file[model]" title="模型"
                            checked>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">对象名称</label>
                            <div class="layui-input-block">
                                <input type="text" value="{{ old('object_name') }}" name="object_name" lay-verify="required" autocomplete="off" placeholder="对象 中文名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">Model 名称</label>
                            <div class="layui-input-block">
                                <input type="text" value="{{ old('model_name') }}" name="model_name" lay-verify="required" autocomplete="off" placeholder="英文 大驼峰单数形式" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline">
                            <div class="layui-input-block">
                                <input type="radio" value="main" name="table_type" title="业务表" />
                                <input type="radio" value="derive" name="table_type" title="衍生表" checked />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-header"><a id="tableBodyAddBox" class="layui-btn layui-btn-sm"><strong>+ 新增字段</strong></a></div>
                <div class="layui-card-body" style="padding: 15px;">

                    <div class="table_body">

                        <!--{{ $i = 0 }}-->
                        @if(old('table_col'))
                        @foreach(old('table_col') as $col)
                        <!--{{ $i ++ }}-->
                        <fieldset class="layui-elem-field">
                            <legend>
                                <div class="layui-input-inline">
                                    <input type="text" name="table_col[{{ $i }}][name]" value="{{ $col['name'] }}" placeholder="字段中文描述" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">name</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="table_col[{{ $i }}][key]" value="{{ $col['key'] }}" placeholder="键名" lay-verify="required" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">字段类型</label>
                                        <div class="layui-input-block">
                                            <select name="table_col[{{ $i }}][type]" lay-search="">

                                                <option value="integer" @if($col['type'] == 'integer') selected @endif >integer</option>
                                                <option value="foreign" @if($col['type'] == 'foreign') selected @endif >外键</option>
                                                <option value="integer-index" @if($col['type'] == 'integer-index') selected @endif >integer-index</option>
                                                <option value="integer-unique" @if($col['type'] == 'integer-unique') selected @endif >integer-unique</option>
                                                <option value="string" @if($col['type'] == 'string') selected @endif >string</option>
                                                <option value="string-index" @if($col['type'] == 'string-index') selected @endif >string-index</option>
                                                <option value="string-unique" @if($col['type'] == 'string-unique') selected @endif >string-unique</option>
                                                <option value="tinyInteger" @if($col['type'] == 'tinyInteger') selected @endif >tinyInteger</option>
                                                <option value="text" @if($col['type'] == 'text') selected @endif >text</option>
                                                <option value="image" @if($col['type'] == 'image') selected @endif >image</option>
                                                <option value="decimal" @if($col['type'] == 'decimal') selected @endif >decimal</option>
                                                <option value="timestamp" @if($col['type'] == 'timestamp') selected @endif >timestamp</option>
                                                <option value="primary" @if($col['type'] == 'primary') selected @endif >主键</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <a class="layui-btn layui-btn-sm layui-btn-danger" onclick="delParent(this)">删除</a>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        @endforeach
                        @else
                        <fieldset class="layui-elem-field">
                            <legend>
                                <div class="layui-input-inline">
                                    <input type="text" name="table_col[0][name]" placeholder="字段中文描述" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">name</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="table_col[0][key]" value="id" placeholder="键名" lay-verify="required" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">字段类型</label>
                                        <div class="layui-input-block">
                                            <select name="table_col[0][type]" lay-search="">
                                                <option value="integer">integer</option>
                                                <option value="foreign">外键</option>
                                                <option value="integer-index">integer-index</option>
                                                <option value="integer-unique">integer-unique</option>
                                                <option value="string">string</option>
                                                <option value="string-index">string-index</option>
                                                <option value="string-unique">string-unique</option>
                                                <option value="tinyInteger">tinyInteger</option>
                                                <option value="text">text</option>
                                                <option value="image">image</option>
                                                <option value="decimal">decimal</option>
                                                <option value="timestamp">timestamp</option>
                                                <option value="primary" selected>主键</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <a class="layui-btn layui-btn-sm layui-btn-danger" onclick="delParent(this)">删除</a>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        @endif

                    </div>

                    <div class="layui-form-item layui-layout-admin">
                        <div class="layui-input-block">
                            <div class="layui-footer" style="left: 0;">
                                <button class="layui-btn" lay-submit lay-filter="*">立即提交</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <script>
        let search_body_html, table_body_html, key_id = '{{ $i }}';
        layui.config({
        base: '/vendor/curatorc/coder/layui/' //静态资源所在路径
    }).extend({
        index: 'index' //主入口模块
    }).use(['form', 'laydate'], function () {
        var $ = layui.$
        , layer = layui.layer
        , form = layui.form;

        form.render(null, 'component-form-group');

        $('#tableBodyAddBox').on('click', function () {
            key_id ++;
            $('.table_body').append('\n' +
                '                    <fieldset class="layui-elem-field">\n' +
                '                        <legend>\n' +
                '                            <div class="layui-input-inline">\n' +
                '                                <input type="text" name="table_col['+key_id+'][name]" placeholder="字段中文描述" lay-verify="required" autocomplete="off" class="layui-input">\n' +
                '                            </div>\n' +
                '                        </legend>\n' +
                '                        <div class="layui-field-box">\n' +
                '                            <div class="layui-form-item">\n' +
                '                                <div class="layui-inline">\n' +
                '                                    <label class="layui-form-label">name</label>\n' +
                '                                    <div class="layui-input-inline">\n' +
                '                                        <input type="text" name="table_col['+key_id+'][key]" placeholder="键名" lay-verify="required" autocomplete="off" class="layui-input">\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                                <div class="layui-inline">\n' +
                '                                    <label class="layui-form-label">字段类型</label>\n' +
                '                                    <div class="layui-input-block">\n' +
                '                                        <select name="table_col['+key_id+'][type]">\n' +
                '                                            <option value="integer">integer</option>\n' +
                '                                            <option value="foreign">外键</option>\n' +
                '                                            <option value="integer-index">integer-index</option>\n' +
                '                                            <option value="integer-unique">integer-unique</option>\n' +
                '                                            <option value="string">string</option>\n' +
                '                                            <option value="string-index">string-index</option>\n' +
                '                                            <option value="string-unique">string-unique</option>\n' +
                '                                            <option value="tinyInteger">tinyInteger</option>\n' +
                '                                            <option value="text">text</option>\n' +
                '                                            <option value="image">image</option>\n' +
                '                                            <option value="decimal">decimal</option>\n' +
                '                                            <option value="timestamp">timestamp</option>\n' +
                '                                            <option value="primary">主键</option>\n' +
                '                                        </select>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                                <div class="layui-inline">\n' +
                '                                    <a class="layui-btn layui-btn-sm layui-btn-danger" onclick="delParent(this)">删除</a>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </fieldset>');
form.render('select');
});

$('.layui-form-checkbox').on('click', function () {
    layer.tips(this.previousElementSibling.attributes.tips.value, this, {
        tips: 1
    });
})
});

function delParent(delBtn) {
    $(delBtn).parent().parent().parent().parent().remove();
}

</script>
</body>
</html>