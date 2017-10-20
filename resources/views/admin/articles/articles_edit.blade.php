@extends('templates.admin.layout')

@push('stylesheets')
    <link rel="stylesheet" href="{{ url('asset/sirtrevorjs/sir-trevor.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ url('css/sir-trevor-custom.css')}}" type="text/css">
@endpush

@section('content')
<div class="">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit article <a href="{{route('articles.index')}}" class="btn btn-info btn-xs"><i class="fa fa-chevron-left"></i> Back </a></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form method="post" action="{{ route('articles.update', ['id' => $article->id]) }}" data-parsley-validate class="form-horizontal form-label-left" enctype="multipart/form-data">
                        @include('blocks.form_input', ['name' => 'title', 'label' => 'Title', 'value' => $article->title, 'required' => true])


                        @include('blocks.form_input', ['name' => 'meta_title', 'label' => 'Meta Title', 'value' => $article->meta_title])
                        
                        @include('blocks.form_input', ['name' => 'meta_description', 'label' => 'Meta Description', 'value' => $article->meta_description])
                        
                        @include('blocks.form_input', ['name' => 'meta_keywords', 'label' => 'Meta Keywords', 'value' => $article->meta_keywords])
                        
                        @include('blocks.form_input', ['name' => 'content_description', 'label' => 'Content Description', 'value' => $article->content_description])

                        @include('blocks.form_input', ['name' => 'external_url', 'label' => 'External Url', 'value' => $article->external_url])


                        @include('blocks.form_tags', ['name' => 'publication', 'label' => 'Publication', 'tags' => $tags, 'selected' => !empty($article->publication) ? $article->publication->id : ''])

                        @include('blocks.form_tags', ['name' => 'brand', 'label' => 'Brand', 'tags' => $tags, 'selected' => !empty($article->brand) ? $article->brand->id : ''])

                        @include('blocks.form_tags', ['name' => 'influencer', 'label' => 'Influencer', 'tags' => $tags, 'selected' => !empty($article->influencer) ? $article->influencer->id : ''])

                        @include('blocks.form_tags', ['name' => 'category', 'label' => 'Category', 'tags' => $tags, 'selected' => !empty($article->category) ? $article->category->id : '', 'required' => true])

                        @include('blocks.form_multiple_tags', ['name' => 'subcategories', 'label' => 'Subcategories', 'tags' => $article->category->children, 'selectedTags' => $article->subcategories])


                        @include('blocks.form_select', ['name' => 'status', 'label' => 'Status', 'items' => $statuses, 'selected' => $article->status, 'required' => true])

                        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Content</label>
                          <div class="col-md-6 col-sm-6 col-xs-12">

                            <textarea id="content" name="content" class="sir-trevor editable">{{ $article->editorContent }}</textarea>
                            @if ($errors->has('content'))
                            <span class="help-block">{{ $errors->first('content') }}</span>
                            @endif
                          </div>
                        </div>

                        <div class="ln_solid"></div>

                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <input name="_method" type="hidden" value="PUT">
                                <button type="submit" class="btn btn-success">Save article Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ url('asset/sirtrevorjs/sir-trevor.js')}}" type="text/javascript"></script>
    <script src="{{ asset('js/sir-trevor.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/sir-trevor-custom.js') }}"></script>
@endpush
