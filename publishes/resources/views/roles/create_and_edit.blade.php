@extends('layouts.app')

@section('title','新增职位')

@section('content')
    @if(isset($role->id) && $role->id == true)
        <form class="layui-form" action="{{ route('roles.update', $role->id) }}" method="POST"
              lay-filter="component-form-group">
            <input type="hidden" name="_method" value="PUT">
            @else
                <form class="layui-form" action="{{ route('roles.store') }}" method="POST"
                      lay-filter="component-form-group">
                    @endif

                    @csrf


                    <div class="layui-fluid">
                        <div class="layui-card">
                            <div class="layui-card-header">职位资料</div>
                            <div class="layui-card-body" style="padding: 15px;">

                                <div class="layui-form-item">
                                    <label class="layui-form-label">职位</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="name" placeholder="职位"
                                               value="{{ old('name', $role->name) }}" lay-verify="required"
                                               autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-card">
                            <div class="layui-card-header">特殊权限</div>
                            <div class="layui-card-body" style="padding: 15px;">
                                <div class="layui-form-item">
                                    <div class="layui-col-md12 layui-col-sm12">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="特殊权限">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox"
                                                           tips="拥有超级权限可以查看所有系统用户的信息，而非超级权限用户只能查看自己和下级的信息。超级权限可以修改职位和权限信息，可以修改所有系统配置信息。<span style='color: red;'>必须确保至少有一个账户为超级管理员<span/>"
                                                           name="超级权限" title="超级权限"
                                                        {{ in_array('超级权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="添加和修改系统用户，修改系统配置，修改合同从属关系"
                                                           name="管理权限" title="管理权限"
                                                        {{ in_array('管理权限', $permissions)?'checked':''}}>
                                                    <input type="checkbox" tips="查看数据汇总统计图"
                                                           name="统计-查看权限" title="数据统计"
                                                        {{ in_array('统计-查看权限', $permissions)?'checked':''}}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>


                                    <div class="layui-col-md4 layui-col-sm4">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="客户">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看客户信息，客户列表"
                                                           name="客户-查看权限" title="查看"
                                                        {{ in_array('客户-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增客户数据"
                                                           name="客户-新增权限" title="新增"
                                                        {{ in_array('客户-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="修改客户资料"
                                                           name="客户-修改权限" title="修改"
                                                        {{ in_array('客户-修改权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除客户"
                                                           name="客户-删除权限" title="删除"
                                                        {{ in_array('客户-删除权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md4 layui-col-sm4">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="跟单">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看跟单信息，跟单记录"
                                                           name="跟单-查看权限" title="查看"
                                                        {{ in_array('跟单-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增跟单数据"
                                                           name="跟单-新增权限" title="新增"
                                                        {{ in_array('跟单-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="修改跟单资料"
                                                           name="跟单-修改权限" title="修改"
                                                        {{ in_array('跟单-修改权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除跟单"
                                                           name="跟单-删除权限" title="删除"
                                                        {{ in_array('跟单-删除权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md4 layui-col-sm4">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="公司">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看公司信息，公司列表"
                                                           name="公司-查看权限" title="查看"
                                                        {{ in_array('公司-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增公司数据"
                                                           name="公司-新增权限" title="新增"
                                                        {{ in_array('公司-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="修改公司资料"
                                                           name="公司-修改权限" title="修改"
                                                        {{ in_array('公司-修改权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除公司"
                                                           name="公司-删除权限" title="删除"
                                                        {{ in_array('公司-删除权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md7 layui-col-sm7">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="合同">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看合同信息，合同列表"
                                                           name="合同-查看权限" title="查看"
                                                        {{ in_array('合同-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增合同数据"
                                                           name="合同-新增权限" title="新增"
                                                        {{ in_array('合同-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="修改合同资料"
                                                           name="合同-修改权限" title="修改"
                                                        {{ in_array('合同-修改权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除未审核及被驳回的合同"
                                                           name="合同-删除权限" title="删除"
                                                        {{ in_array('合同-删除权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="合同撤单操作"
                                                           name="合同-撤单权限" title="撤单"
                                                        {{ in_array('合同-撤单权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="审核合同是否有效"
                                                           name="合同-审核权限" title="审核"
                                                        {{ in_array('合同-审核权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="上传合同资料"
                                                           name="合同-上传权限" title="上传"
                                                        {{ in_array('合同-上传权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="下载合同资料"
                                                           name="合同-下载权限" title="下载"
                                                        {{ in_array('合同-下载权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md2 layui-col-sm2">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="年审">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="接受年审分配合同"
                                                           name="年审-查看权限" title="接单"
                                                        {{ in_array('年审-查看权限', $permissions)?'checked':'' }}>

                                                    <input type="checkbox" tips="分配年审合同"
                                                           name="年审-修改权限" title="分配"
                                                        {{ in_array('年审-修改权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md3 layui-col-sm3">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="成本">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看成本信息，成本列表"
                                                           name="成本-查看权限" title="查看"
                                                        {{ in_array('成本-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增成本数据"
                                                           name="成本-新增权限" title="新增"
                                                        {{ in_array('成本-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="修改成本资料"
                                                           name="成本-修改权限" title="修改"
                                                        {{ in_array('成本-修改权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md4 layui-col-sm4">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="任务">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="新增任务数据"
                                                           name="任务-新增权限" title="新增"
                                                        {{ in_array('任务-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="修改任务资料"
                                                           name="任务-修改权限" title="修改"
                                                        {{ in_array('任务-修改权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除任务"
                                                           name="任务-删除权限" title="删除"
                                                        {{ in_array('任务-删除权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md4 layui-col-sm4">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="咨询">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看咨询任务"
                                                           name="咨询-查看权限" title="查看"
                                                        {{ in_array('咨询-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="分配咨询任务"
                                                           name="咨询-分配权限" title="分配"
                                                        {{ in_array('咨询-分配权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="上传咨询材料"
                                                           name="咨询-上传权限" title="上传"
                                                        {{ in_array('咨询-上传权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="下载咨询材料"
                                                           name="咨询-下载权限" title="下载"
                                                        {{ in_array('咨询-下载权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md4 layui-col-sm4">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="申报">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看申报任务"
                                                           name="申报-查看权限" title="查看"
                                                        {{ in_array('申报-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="申报任务"
                                                           name="申报-申报权限" title="申报"
                                                        {{ in_array('申报-申报权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="上传申报证书（案卷）"
                                                           name="申报-上传权限" title="上传"
                                                        {{ in_array('申报-上传权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="下载申报证书（案卷）"
                                                           name="申报-下载权限" title="下载"
                                                        {{ in_array('申报-下载权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>


                                    <div class="layui-col-md6 layui-col-sm6">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="费用">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看费用信息，费用列表"
                                                           name="费用-查看权限" title="查看"
                                                        {{ in_array('费用-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增费用数据"
                                                           name="费用-新增权限" title="新增"
                                                        {{ in_array('费用-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="审核到账情况"
                                                           name="费用-审核权限" title="审核"
                                                        {{ in_array('费用-审核权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除费用"
                                                           name="费用-删除权限" title="删除"
                                                        {{ in_array('费用-删除权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md6 layui-col-sm6">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="提成">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看提成信息，提成列表"
                                                           name="提成-查看权限" title="查看"
                                                        {{ in_array('提成-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="发放提成"
                                                           name="提成-新增权限" title="发放"
                                                        {{ in_array('提成-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除提成"
                                                           name="提成-删除权限" title="删除"
                                                        {{ in_array('提成-删除权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md6 layui-col-sm6">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="发票">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看发票信息，发票列表"
                                                           name="发票-查看权限" title="查看"
                                                        {{ in_array('发票-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="申请开票"
                                                           name="发票申请-新增权限" title="申请"
                                                        {{ in_array('发票申请-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增发票数据，驳回发票申请"
                                                           name="发票-新增权限" title="开票"
                                                        {{ in_array('发票-新增权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md6 layui-col-sm6">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="证书">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看证书信息，证书列表"
                                                           name="证书-查看权限" title="查看"
                                                        {{ in_array('证书-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="申领证书"
                                                           name="证书-新增权限" title="申领"
                                                        {{ in_array('证书-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="批复发放证书"
                                                           name="证书-修改权限" title="发放"
                                                        {{ in_array('证书-修改权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="layui-col-md6 layui-col-sm6">
                                        <fieldset class="layui-elem-field">
                                            <legend>
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" style="border: none"
                                                           value="合作方">
                                                </div>
                                            </legend>
                                            <div class="layui-field-box">
                                                <div class="layui-form-item">
                                                    <input type="checkbox" tips="查看合作方信息，合作方列表"
                                                           name="合作方-查看权限" title="查看"
                                                        {{ in_array('合作方-查看权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="新增合作方数据"
                                                           name="合作方-新增权限" title="新增"
                                                        {{ in_array('合作方-新增权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="修改合作方资料"
                                                           name="合作方-修改权限" title="修改"
                                                        {{ in_array('合作方-修改权限', $permissions)?'checked':'' }}>
                                                    <input type="checkbox" tips="删除合作方"
                                                           name="合作方-删除权限" title="删除"
                                                        {{ in_array('合作方-删除权限', $permissions)?'checked':'' }}>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    {{-- 权限编辑标记，请勿删除 --}}

                                </div>
                            </div>
                        </div>
                    </div>

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
                @stop

            @section('script')

                <script>
                    layui.use(['index', 'form'], function () {
                        var $ = layui.$
                            , admin = layui.admin
                            , element = layui.element
                            , layer = layui.layer
                            , form = layui.form;

                        form.render(null, 'component-form-group');


                        form.on('submit', function (data) {
                            layerLoading();
                        });


                        $('.layui-form-checkbox').on('mouseenter', function () {
                            layer.tips(this.previousElementSibling.attributes.tips.value, this, {
                                tips: 1
                            });
                        })

                    });
                </script>
@stop
