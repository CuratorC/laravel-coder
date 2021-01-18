<?php
namespace CuratorC\Coder\Traits;

trait Blade
{
    public function bladePath()
    {
        // 创建必要目录
        $this->createDir(app_path().'/../resources/views/' . $this->models);

        $this->createIndexBladeContent();
        $this->createCreateAndEditBladeContent();
        $this->createShowContent();

        $this->createCardContent();

        $this->createSearchHtmlContent();
        $this->createSearchScriptContent();
        $this->createTableHtmlContent();
        $this->createTableScriptContent();
    }

    public function createIndexBladeContent()
    {
        $text = '@extends(\'layouts.app\')

@section(\'title\',\'' . $this->param['object_name'] . '列表\')

@section(\'content\')

<div class="layui-fluid">
  <div class="layui-row layui-col-space15">
    <div class="layui-col-md12 layui-col-sm12">

      {{--搜索域--}}
      @include(\'' . $this->models . '._search_html\')
      {{--表格--}}
      @include(\'' . $this->models . '._table_html\')

    </div>
  </div>
</div>
@endsection

@section(\'script\')
<script>
  var coderLayer;
  layui.use([\'index\', \'form\', \'table\', \'laydate\', \'coderTable\', \'coderLayer\', \'mobile\'], function() {
    let admin = layui.admin
    , layuiDate = layui.laydate
    , coderTable = layui.coderTable
    , coderLayer = layui.coderLayer
    , ' . $this->model . 'Where;

    {{--搜索域--}}
    @include(\'' . $this->models . '._search_script\')

    {{--表格--}}
    @include(\'' . $this->models . '._table_script\')
  });
</script>
@endsection
';
        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/index.blade.php',
            $text
        );
    }

    private function createCreateAndEditBladeContent()
    {
        $text = '@extends(\'layouts.app\')

@section(\'title\',\'新增' . $this->param['object_name'] . '\')

@section(\'content\')
<div class="layui-fluid">
  <div class="layui-card">
    <div class="layui-card-header">' . $this->param['object_name'] . '资料</div>
    <div class="layui-card-body" style="padding: 15px;">
      @if($' . $this->model . '->is_new_model())
      <form class="layui-form" action="{{ route(\'' . $this->models . '.store\') }}" method="POST"
        lay-filter="component-form-group">
        @else
      <form class="layui-form" action="{{ route(\'' . $this->models . '.update\', $' . $this->model . '->id) }}" method="POST"
        lay-filter="component-form-group">
        <input type="hidden" name="_method" value="PUT">
        @endif

        @csrf

        ';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="text" placeholder="' . $col['name'] . '" value="{{ old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ') }}" autocomplete="off" name="' . $col['key'] . '"  onkeyup="checkInputIntFloat(this);" class="layui-input">
          </div>
        </div>';

                break;
                case 'foreign':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <select name="' . $col['key'] . '_id" lay-search="">
              <option value="" selected disabled>选择' . $col['name'] . '</option>
              @foreach($' . $this->pluralize($col['key']) . ' as $item)
              <option value="{{ $item->id }}" @if( $item->id == old(\'' . $col['key'] . '_id\', $' . $this->model . '->' . $col['key'] . '_id)) selected @endif >{{ $item->name }}</option>
              @endforeach
            </select>
          </div>
        </div>';

                break;
                case 'integer-index':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="text" placeholder="' . $col['name'] . '" value="{{ old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ') }}" autocomplete="off" name="' . $col['key'] . '"  onkeyup="checkInputIntFloat(this);" class="layui-input">
          </div>
        </div>';

                break;
                case 'integer-unique':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="text" placeholder="' . $col['name'] . '" value="{{ old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ') }}" autocomplete="off" name="' . $col['key'] . '"  onkeyup="checkInputIntFloat(this);" class="layui-input">
          </div>
        </div>';

                break;
                case 'string':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="text" placeholder="' . $col['name'] . '" value="{{ old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ') }}" autocomplete="off" name="' . $col['key'] . '" class="layui-input">
          </div>
        </div>';

                break;
                case 'string-index':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="text" placeholder="' . $col['name'] . '" value="{{ old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ') }}" autocomplete="off" name="' . $col['key'] . '" class="layui-input">
          </div>
        </div>';

                break;
                case 'string-unique':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="text" placeholder="' . $col['name'] . '" value="{{ old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ') }}" autocomplete="off" name="' . $col['key'] . '" class="layui-input">
          </div>
        </div>';

                break;
                case 'tinyInteger':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="radio" name="' . $col['key'] . '" value="1" title="正常" checked>
            <input type="radio" name="' . $col['key'] . '" value="9" title="关闭">
          </div>
        </div>';

                break;
                case 'text':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <textarea name="' . $col['key'] . '" placeholder="请输入内容" class="layui-textarea"></textarea>
          </div>
        </div>';

                break;
                case 'image':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            图片上传待补全
          </div>
        </div>';

                break;
                case 'decimal':
                $text .= '
        <div class="layui-form-item">
          <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
            <input type="text" placeholder="' . $col['name'] . '" value="{{ formatDecimal(old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ')) }}" autocomplete="off" name="' . $col['key'] . '" onkeyup="checkInputIntFloat(this);" class="layui-input">
          </div>
        </div>';

                break;
                case 'timestamp':
                $text .= '
        <div class="layui-form-item">
            <label class="layui-form-label">' . $col['name'] . '</label>
            <div class="layui-input-block">
                <input type="text" name="' . $col['key'] . '" id="' . $col['key'] . '" value="{{ old(\'' . $col['key'] . '\', $' . $this->model . '->' . $col['key'] . ') }}"
                    placeholder="' . $col['name'] . '" class="layui-input" autocomplete="off" lay-verify="datetime">
            </div>
        </div>';

                break;
                case 'primary':

                break;
                default:
            }
        }
        $text .= '

        <div class="layui-form-item layui-layout-admin">
          <div class="layui-input-block">
            <div class="layui-footer" style="left: 0;">
              <button class="layui-btn" type="submit" lay-submit=""
              lay-filter="data_form">立即提交
            </button>
            <input type="reset" class="layui-btn layui-btn-primary"/>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
@stop

@section(\'script\')

<script>
  layui.use([\'index\', \'form\', \'laydate\'], function () {
    var $ = layui.$
    , admin = layui.admin
    , element = layui.element
    , layer = layui.layer
    , layuiDate = layui.laydate
    , form = layui.form;

    form.render(null, \'component-form-group\');

';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':

                break;
                case 'foreign':

                break;
                case 'integer-index':

                break;
                case 'integer-unique':

                break;
                case 'string':

                break;
                case 'string-index':

                break;
                case 'string-unique':

                break;
                case 'tinyInteger':

                break;
                case 'text':

                break;
                case 'image':
                $text .= '
    // 图片上传待补全
    ';
                break;
                case 'decimal':

                break;
                case 'timestamp':
                $text .= '
    // ' . $col['name'] . '
    layuiDate.render({
      elem: \'#' . $col['key'] . '\'
      ,type: \'datetime\'
      , trigger: \'click\'
    });
    ';

                break;
                case 'primary':

                break;
                default:
            }
        }
        $text .= '

  });
</script>
@endsection
';

        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/create_and_edit.blade.php',
            $text
        );
    }

    private function createShowContent()
    {
        $text = '@extends(\'layouts.app\')

@section(\'title\',\'' . $this->param['object_name'] . '资料\')

@section(\'content\')

<div class="layui-fluid layadmin-maillist-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12 layui-col-sm12">

            {{--卡片信息--}}
            @include(\'' . $this->models . '._card\')

        </div>';

        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'foreign':
                    $text .='

        <div class="layui-col-md12 layui-col-sm12">

            {{--' . $col['name'] . '信息--}}
            @include(\'' . $this->pluralize($col['key']) . '._card\', [\'' . $col['key'] . '\' => $' . $this->model . '->' . $col['key'] . '])

        </div>
        ';

                    break;
                default:
            }
        }



        $text .= '

    </div>
</div>
@endsection

@section(\'script\')
<script>
    layui.use([\'index\', \'form\'], function() {
    });
</script>
@endsection
';
        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/show.blade.php',
            $text
        );
    }

    public function createCardContent()
    {
        $text = '<div class="layadmin-contact-box">
  <div class="layui-col-md12 layui-col-sm12">
    <div class="layadmin-homepage-panel">
      <div class="text-center">
        <h4 class="layadmin-homepage-font"> ' . $this->param['object_name'] . ': {{ $' . $this->model . '->name }} </h4>
        <br />
        ';

        if (in_array('status', $this->defaultCols)) {
            $text .= '@switch ($' . $this->model . '->status)
        @case(1)
        <label class="layui-btn layui-btn-xs">正常</label>
        @break
        @case(9)
        <label class="layui-btn layui-btn-xs layui-btn-danger">冻结</label>
        @break
        @default
        <label class="layui-btn layui-btn-xs layui-btn-primary">未知</label>
        @endswitch';
        }

        $text .= '
      </div>
    </div>


    <div class="layui-form-item">';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ $' . $this->model . '->' . $col['key'] . ' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';
                break;
                case 'foreign':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ isset($' . $this->model . '->' . $col['key'] . ')?$' . $this->model . '->' . $col['key'] . '->name:\'\' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';

                break;
                case 'integer-index':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ $' . $this->model . '->' . $col['key'] . ' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';

                break;
                case 'integer-unique':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ $' . $this->model . '->' . $col['key'] . ' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';

                break;
                case 'string':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ $' . $this->model . '->' . $col['key'] . ' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';
                break;
                case 'string-index':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
          <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ $' . $this->model . '->' . $col['key'] . ' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';

                break;
                case 'string-unique':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ $' . $this->model . '->' . $col['key'] . ' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';

                break;
                case 'tinyInteger':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ config(\'tinyIntegerMap.' . $this->models . '.' . $col['key'] . '.\' . $' . $this->model . '->' . $col['key'] . ') }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';

                break;
                case 'text':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <textarea style="border: none" class="layui-textarea">{{ $' . $this->model . '->' . $col['key'] . ' }}</textarea>
        </div>
      </div>
        ';

                break;
                case 'image':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <img style="max-width: 50px; cursor:pointer" onclick="showBigImage(\'{{ $' . $this->model . '->' . $col['key'] . ' }}\')" src="{{ $' . $this->model . '->' . $col['key'] . ' }}" alt="图片读取失败"/>
        </div>
      </div>
        ';

                break;
                case 'decimal':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ formatMoney($' . $this->model . '->' . $col['key'] . ') }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';

                break;
                case 'timestamp':
                $text .= '
      <div class="layui-inline">
        <label class="layui-form-label">' . $col['name'] . '</label>
        <div class="layui-input-block">
          <strong>
            <input style="border: none" value="{{ (string)$' . $this->model . '->' . $col['key'] . ' }}" disabled placeholder="" class="layui-input">
          </strong>
        </div>
      </div>
        ';
                break;
                case 'primary':

                break;
                default:
            }
        }
        $text .= '

    </div>
  </div>
</div>';

        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/_card.blade.php',
            $text
        );
    }

    private function createSearchHtmlContent()
    {
        $text = '<div class="layui-card">
  <div class="layui-card-header">搜索域</div>
  <div class="layui-card-body layui-row layui-col-space10 layui-form">
  ';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '

    <div class="layui-col-sm2">
      <input type="text" name="' . $col['key'] . '" placeholder="' . $col['name'] . '" autocomplete="off" class="layui-input">
    </div>';
                break;
                case 'foreign':
                $text .= '

    <div class="layui-col-sm2">
      <select name="' . $col['key'] . '_id" lay-search="">
        <option value="">' . $col['name'] . '</option>
        @foreach($' . $this->pluralize($col['key']) . ' as $item)
        <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
      </select>
    </div>';

                break;
                case 'integer-index':
                $text .= '
    <div class="layui-col-sm2">
      <input type="text" name="' . $col['key'] . '" placeholder="' . $col['name'] . '" autocomplete="off" class="layui-input">
    </div>';

                break;
                case 'integer-unique':
                $text .= '

    <div class="layui-col-sm2">
      <input type="text" name="' . $col['key'] . '" placeholder="' . $col['name'] . '" autocomplete="off" class="layui-input">
    </div>';

                break;
                case 'string':
                $text .= '

    <div class="layui-col-sm2">
      <input type="text" name="' . $col['key'] . '" placeholder="' . $col['name'] . '" autocomplete="off" class="layui-input">
    </div>';

                break;
                case 'string-index':
                $text .= '

    <div class="layui-col-sm2">
      <input type="text" name="' . $col['key'] . '" placeholder="' . $col['name'] . '" autocomplete="off" class="layui-input">
    </div>';

                break;
                case 'string-unique':
                $text .= '

    <div class="layui-col-sm2">
      <input type="text" name="' . $col['key'] . '" placeholder="' . $col['name'] . '" autocomplete="off" class="layui-input">
    </div>';

                break;
                case 'tinyInteger':
                $text .= '

    <div class="layui-col-sm2">
      <select name="' . $col['key'] . '" lay-search="">
        <option value="">' . $col['name'] . '</option>
        @foreach(config(\'tinyIntegerMap.' . $this->models . '.' . $col['key'] . '\') as $key => $item)
        <option value="{{ $key }}">{{ $item }}</option>
        @endforeach
      </select>
    </div>';

                break;
                case 'text':

                break;
                case 'image':

                break;
                case 'decimal':

                break;
                case 'timestamp':
                $text .= '

    <div class="layui-col-md2">
      <input id="own_input_data_' . $col['key'] . '" name="' . $col['key'] . '" placeholder="' . $col['name'] . '时间段" type="text" autocomplete="off" class="layui-input">
    </div>';

                break;
                case 'primary':
                $text .= '

    <div class="layui-col-sm2">
      <input type="text" name="' . $col['key'] . '" placeholder="' . $col['name'] . '" autocomplete="off" class="layui-input">
    </div>';
                break;
                default:
            }
        }

        if (in_array('status', $this->defaultCols)) {
            $text .= '

    <div class="layui-col-sm2">
      <select name="status" lay-search="">
        <option value="">状态</option>
        @foreach(config(\'tinyIntegerMap.' . $this->models . '.status\') as $key => $item)
        <option value="{{ $key }}">{{ $item }}</option>
        @endforeach
      </select>
    </div>';
        }

        if (in_array('timestamps', $this->defaultCols)) {
            $text .= '

    <div class="layui-col-md2">
      <input id="own_input_data_created_at" name="created_at" placeholder="创建时间段" type="text" autocomplete="off" class="layui-input">
    </div>';
        }

        $text .= '
    <div class="layui-col-sm2">
      <button id="searchData" type="submit" class="layui-btn layui-btn-fluid">搜索</button>
    </div>
  </div>
</div>
';

        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/_search_html.blade.php',
            $text
        );
    }

    private function createSearchScriptContent()
    {
        $text = 'coderTable.reloadWhere = function() {
    coderTable.where = {';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'foreign':
                $text .= '
        ' . $col['key'] . '_id: $("select[name=' . $col['key'] . '_id]").val(),';

                break;
                case 'integer-index':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'integer-unique':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'string':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'string-index':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'string-unique':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'tinyInteger':
                $text .= '
        ' . $col['key'] . ': $("select[name=' . $col['key'] . ']").val(),';

                break;
                case 'text':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'image':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'decimal':

                break;
                case 'timestamp':
                $text .= '
        ' . $col['key'] . ': $("input[name=' . $col['key'] . ']").val(),';

                break;
                case 'primary':

                break;
                default:
            }
        }

        if (in_array('status', $this->defaultCols)) {
            $text .= '
        status: $("select[name=status]").val(),';
        }

        if (in_array('timestamps', $this->defaultCols)) {
            $text .= '
        created_at: $("input[name=status]").val(),';
        }

        $text .= '
    }
}
';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'image':
                $text .= '
// 图片上传待补全';

                break;
                case 'timestamp':
                $text .= '
// ' . $col['name'] . '时间段
layuiDate.render({
    elem: \'#own_input_data_' . $col['key'] . '\'
    , range: true
});';
                break;
                default:
            }
        }


        if (in_array('timestamps', $this->defaultCols)) {
            $text .= '
// 创建时间段
layuiDate.render({
    elem: \'#own_input_data_created_at\'
    , range: true
});';
        }
        $text .= '


coderTable.reloadWhere();
';

        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/_search_script.blade.php',
            $text
        );
    }

    private function createTableHtmlContent()
    {
        $text = '<div class="layui-card">
  <div class="layui-card-header">' . $this->param['object_name'] . '列表</div>
  <div class="layui-card-body">
    <table class="layui-hide" id="' . $this->models . '-table" lay-filter="' . $this->models . '-table"></table>
    {{-- 头部操作按钮 --}}
    <script type="text/html" id="' . $this->models . '-toolbar">
      <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="add"><i class="layui-icon layui-icon-add-1"></i>新增</button>
      </div>
    </script>
    {{-- 操作栏按钮 --}}
    <script type="text/html" id="' . $this->models . '-tool">
      <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="remark">备注</a>
      <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="show">查看</a>
      <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
      <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="destroy">删除</a>
    </script>
  </div>
</div>
';

        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/_table_html.blade.php',
            $text
        );
    }

    private function createTableScriptContent()
    {
        $text = '// 查询条件
coderTable.where = ' . $this->model . 'Where
// 模块名（下划线复数形式）
coderTable.models = \'' . $this->models . '\';
// 模块中文名
coderTable.name = \'' . $this->param['object_name'] . '\';
// 请求地址
coderTable.url = \'{{ route(\'' . $this->models . '.list\') }}\';
// 字段
coderTable.cols = [';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';

                break;
                case 'foreign':
                $text .= '
, {field: \'' . $col['key'] . '_id\', title: \'' . $col['name'] . '\', width:110, sort:true, templet: function(res) {
  if (res.' . $col['key'] . ') return res.' . $col['key'] . '.name;
  else return \'\';
}}';

                break;
                case 'integer-index':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';
                break;
                case 'integer-unique':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';
                break;
                case 'string':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';
                break;
                case 'string-index':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';
                break;
                case 'string-unique':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';
                break;
                case 'tinyInteger':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width:110, sort:true, templet: function(res) {
  switch(res.status) {
    case 1:
    return \'<button class="layui-btn layui-btn-xs" lay-event="close">正常</button>\';
    case 9:
    return \'<button class="layui-btn layui-btn-xs layui-btn-danger" lay-event="open">冻结</button>\';
    default:
    return \'<button class="layui-btn layui-btn-xs layui-btn-primary">未知</button>\';
  }
}}';

                break;
                case 'text':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', minWidth: 110}';
                break;
                case 'image':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';

                break;
                case 'decimal':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 120}';

                break;
                case 'timestamp':
                $text .= '
, {field: \'' . $col['key'] . '\', title: \'' . $col['name'] . '\', width: 160}';

                break;
                case 'primary':
                $text .= '
{field: \'id\', title: \'' . $this->param['object_name'] . '\', fixed: is_mobile()?false:\'left\', width: 90, sort: true}';
                if (in_array('status', $this->defaultCols)) {
                    $text .= '
, {field: \'status\', title: \'状态\', width:110, sort:true, templet: function(res) {
  switch(res.status) {
    case 1:
    return \'<button class="layui-btn layui-btn-xs" lay-event="close">正常</button>\';
    case 9:
    return \'<button class="layui-btn layui-btn-xs layui-btn-danger" lay-event="open">冻结</button>\';
    default:
    return \'<button class="layui-btn layui-btn-xs layui-btn-primary">未知</button>\';
  }
}}';
                }
                break;
                default:
            }
        }

        if (in_array('timestamps', $this->defaultCols)) {
                    $text .= '
, {field: \'created_at\', title: \'创建时间\', width: 160}';
        }

        if (in_array('admin_remark', $this->defaultCols)) {
                    $text .= '
, {field: \'admin_remark\', title: \'备注\', minWidth:200}';
        }

        $text .= '
, {fixed: is_mobile()?false:\'right\', title: \'操作\', toolbar: \'#' . $this->models . '-tool\', width:208}
];

// 行工具
coderTable.tool = function (obj) {
  let data = obj.data;

  switch (obj.event) {
    // 冻结
    case \'close\':
    coderLayer.put({
      title: \'确认冻结' . $this->param['object_name'] . ' \' + data.name + \'?\'
      , url: \'{{ route(\'' . $this->models . '.index\') }}/\' + data.id + \'/status/\' + \'9\'
      , success: function() {
        obj.update({status:9})
      }
    });
    break;
    // 解封
    case \'open\':
    coderLayer.put({
      title: \'确认解封' . $this->param['object_name'] . ' \' + data.name + \'?\'
      , url: \'{{ route(\'' . $this->models . '.index\') }}/\' + data.id + \'/status/\' + \'1\'
      , success: function() {
        obj.update({status:1})
      }
    });
    break;
    // 备注
    case \'remark\':
    coderLayer.text({
      title: \'备注\'
      , text: data.admin_remark
      , col: \'admin_remark\'
      , url: \'{{ route(\'' . $this->models . '.index\') }}/\' + data.id + \'/remark/\'
      , success: function(value) {
        obj.update({admin_remark:value})
      }
    });
    break;
    // 查看
    case \'show\':
    coderLayer.open({
      title: \'' . $this->param['object_name'] . ' \' + data.name + \' 信息查看\'
      , url: \'{{ route(\'' . $this->models . '.index\') }}/\' + data.id
    });
    break;
    // 编辑
    case \'edit\':
    coderLayer.open({
      title: \'' . $this->param['object_name'] . ' \' + data.name + \' 资料编辑\'
      , url: \'{{ route(\'' . $this->models . '.index\') }}/\' + data.id + \'/edit\'
    });
    break;

    // 删除
    case \'destroy\':
    coderLayer.destroy({
      title: \'确认删除' . $this->param['object_name'] . ' \' + data.name + \'?\'
      , url: \'{{ route(\'' . $this->models . '.destroy\', \'\') }}/\' + data.id
      , success: function() {
        obj.del()
      }
    });
    break;
    default:
  }
};

// 工具栏
coderTable.toolbar = function (obj) {
  // let checkStatus = table.checkStatus(obj.config.id); //获取选中行状态
  switch (obj.event) {
    // 新增
    case \'add\':
    coderLayer.open({
      title: \'新增' . $this->param['object_name'] . '\'
      , url: \'{{ route(\'' . $this->models . '.create\') }}\'
      , end: function() {
        coderTable.reload();
      }
    });
    break;
    default:
  }
}

// 执行
coderTable.render();
';

        $this->writeToFile(
            app_path() . '/../resources/views/' . $this->models . '/_table_script.blade.php',
            $text
        );
    }

}