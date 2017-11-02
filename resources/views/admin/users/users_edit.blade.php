@extends('layouts.admin')

@section('content')
<div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>@lang('blade_templates.admin.users.edit_user_title') <a href="{{ route('users.index') }}" class="btn btn-info btn-xs"><i class="fa fa-chevron-left"></i> @lang('blade_templates.global.back') </a></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form method="post" action="{{ route('users.update', ['id' => $user->id]) }}" data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        @include('blocks.form_input', ['name' => 'name', 'label' => __('blade_templates.users.name'), 'value' => $user->name, 'required' => true])

                        @include('blocks.form_input', ['name' => 'email', 'label' => __('blade_templates.users.email'), 'value' => $user->email, 'required' => true, 'readonly' => true])

                        @include('blocks.form_select', ['name' => 'role', 'label' => __('blade_templates.users.role'), 'items' => $roles, 'selected' => $user->role, 'required' => true])

                        <div class="ln_solid"></div>

                        <div class="form-group">
                            <div class="{{ HtmlElementsClasses::getHtmlClassForElement('button', 'admin') }}">
                                <input name="_method" type="hidden" value="PUT">
                                <button type="submit" class="btn btn-success">@lang('blade_templates.admin.users.edit_user_button_text')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
