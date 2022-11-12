@extends('layouts.app')

@section('stylesheets')
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<!-- summernote -->
<link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Blog Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
              <li class="breadcrumb-item active title_wrap">{{ $blog->title }}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-2"></div>

          <div class="col-md-8">
            <!-- Input addon -->
            <div class="card card-info">
              <div class="card-header">
                  <h3 class="card-title title_wrap">{{ $blog->title }}</h3>
              </div>
              <form id="update-blog-form" action="javascript: void(0);">
                <div class="card-body">
                  <label>Blog Title</label>
                  <div class="input-group mb-3 title_row">
                    <input type="text" name="blog_title" class="form-control mr-2" placeholder="Blog Title" value="{{ $blog->title }}">
                    
                    <div class="icheck-primary d-inline">
                        <input type="checkbox" name="slug_modify" id="slug_modify" value="1" data-toggle="tooltip" data-placement="top" title="Check this box to regenerate the slug.">
                        <label for="slug_modify"></label>
                    </div>
                  </div>

                  <label>Blog Slug</label>
                  <div class="input-group mb-3 title_row">
                      <input type="text" name="blog_slug" class="form-control mr-2" value="{{ $blog->slug }}" placeholder="Blog Slug" disabled>

                      <div class="icheck-primary d-inline">
                          <input type="checkbox" name="slug_editable" id="slug_editable" value="1" data-toggle="tooltip" data-placement="top" title="Check this box to make the slug field editable.">
                          <label for="slug_editable"></label>
                      </div>
                  </div>

                  @if( $categories )
                    <label>Select Category</label>
                    <div class="form-group">
                      <select name="blog_category" id="blog_category" class="form-control select2bs4" style="width: 100%;">
                        @foreach($categories as $cat)
                          <option value="{{ $cat->id }}" @if($blog->category_id == $cat->id) selected @endif>{{ $cat->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  @endif

                  <label>Blog Content</label>
                  <textarea name="blog_content" id="blog_content">{{ $blog->content }}</textarea>

                  <label>Page Title</label>
                  <div class="input-group mb-3 title_row">
                    <input type="text" name="page_title" class="form-control mr-2" value="{{ $blog->page_title }}" placeholder="Page Title">
                  </div>

                  <label>Meta Data</label>
                  <div class="input-group mb-3 title_row">
                    <textarea name="metadata" id="metadata" class="form-control" rows="3" placeholder="Enter Meta Data">{{ $blog->metadata }}</textarea>
                  </div>

                  <label>Keywords</label>
                  <div class="input-group mb-3 title_row">
                    <textarea name="keywords" id="keywords" class="form-control" rows="3" placeholder="Enter Keywords">{{ $blog->keywords }}</textarea>
                  </div>
                  
                  <label>Status</label>
                  <div class="input-group mb-3 title_row">
                      <div class="icheck-success d-inline mr-5">
                        <input type="radio" name="blog_status" id="status_active" value="1" @if($blog->status == '1')checked @endif>
                        <label for="status_active">Active</label>
                      </div>
                      <div class="icheck-danger d-inline">
                        <input type="radio" name="blog_status" id="status_inactive" value="0" @if($blog->status == '0')checked @endif>
                        <label for="status_inactive">Inactive</label>
                      </div>
                  </div>
                </div>

                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right" id="update-blog-btn">Update</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!--/.col (left) -->

          <div class="col-md-2"></div>

        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('scripts')
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<script>
  $(function () {
    blog_content = $('#blog_content');
    blog_category = $('#blog_category');
    update_blog_form = $('#update-blog-form');

    slug_modify_field = $('input[name="slug_modify"]');
    blog_slug_field = $('input[name="blog_slug"]');

    blog_category.select2({
      theme: 'bootstrap4'
    });

    // Summernote
    blog_content.summernote({
      height: 300
    });

    //++++++++++++++++++++ REGENERATE SLUG :: Start ++++++++++++++++++++//
    $('input[name="blog_title"]').on('blur', function(){
      regenerate_slug();
      if( slug_modify_field.is(':checked') ){
        regenerate_slug();
      }
      else{
        blog_slug_field.val('{{ $blog->slug }}');
      }
    });

    slug_modify_field.on('change', function(){
      if( $(this).is(':checked') ){
        regenerate_slug();
      }
      else{
        blog_slug_field.val('{{ $blog->slug }}');
      }
    });
    //++++++++++++++++++++ REGENERATE SLUG :: End ++++++++++++++++++++//

    $('input[name="slug_editable"]').on('change', function(){
      if( $(this).is(':checked') ){
        blog_slug_field.prop('disabled', false);
      }
      else{
        blog_slug_field.val('{{ $blog->slug }}').prop('disabled', true);
      }
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('#update-blog-btn').on('click', function(){
      blog_title = $('input[name="blog_title"]').val().trim();
      slug_editable = $('input[name="slug_editable"]:checked').val();
      blog_slug = blog_slug_field.val().trim();
      blog_category = blog_category.val()
      blog_content_text = blog_content.summernote('code');
      page_title = $('input[name="page_title"]').val();
      metadata = $('#metadata').val();
      keywords = $('#keywords').val();
      blog_status = $('input[name="blog_status"]:checked').val();

      if( blog_title == '' ){
        swal_fire_error('No blog title found!');
        return false;
      }

      if( (slug_editable == 1) && (blog_slug == '') ){
        swal_fire_error('No blog slug found!');
        return false;
      }

      if( blog_content == '' ){
        swal_fire_error('No blog content found!');
        return false;
      }

      this_obj = $(this);

      this_obj.html('<i class="fa fa-spinner" aria-hidden="true"></i> Updating...').attr('disabled', true);

      //
      $.ajax({
        dataType: 'json',
        type: 'POST',
        data:{
          blog_uuid: '{{ $blog->uuid }}',
          blog_title: blog_title,
          slug_editable: slug_editable,
          blog_slug: blog_slug,
          blog_category: blog_category,
          blog_content: blog_content_text,
          page_title: page_title,
          metadata: metadata,
          keywords: keywords,
          blog_status: blog_status,
        },
        url: "{{ route('blog.update-submit') }}",
        success:function(data) {
          this_obj.html('Update').attr('disabled', false);

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Blog info updated successfully!');

            $('.title_wrap').html(blog_title);
          }

          $('.btn').attr('disabled', false);
          this_obj.html('Update');
        }
      });
      //
    });
    
  });

  function regenerate_slug(){
    blog_title = $('input[name="blog_title"]').val().trim();

    $.ajax({
      dataType: 'json',
      type: 'POST',
      data:{
        blog_title: blog_title,
      },
      url: "{{ route('blog.regenerate-slug') }}",
      success:function(data) {
        if( data.status == 'failed' ){
          swal_fire_error(data.error.message);
          return false;
        }
        else if( data.status == 'success' ){
          blog_slug_field.val(data.blog_slug);
        }
      }
    });
  }

</script>
@endsection
