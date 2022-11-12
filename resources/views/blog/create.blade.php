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
            <h1 class="m-0">Post Blog</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
              <li class="breadcrumb-item active">Post New</li>
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
                  <h3 class="card-title">Post New Blog</h3>
              </div>
              <form id="post-blog-form" action="javascript: void(0);" enctype="multipart/form-data">
                <div class="card-body">
                  <label>Blog Title</label>
                  <div class="input-group mb-3 title_row">
                    <input type="text" name="blog_title" class="form-control mr-2" placeholder="Blog Title">
                  </div>

                  @if( $categories )
                    <label>Select Category</label>
                    <div class="form-group">
                      <select name="blog_category" id="blog_category" class="form-control select2bs4" style="width: 100%;">
                        @foreach($categories as $cat)
                          <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  @endif

                  <label>Blog Content</label>
                  <textarea name="blog_content" id="blog_content"></textarea>
                  
                  <label>Upload Banner</label>
                  <div class="input-group mb-3 title_row">
                    <input class="form-control" type="file" name="banner">
                  </div>

                  <label>Page Title</label>
                  <div class="input-group mb-3 title_row">
                    <input type="text" name="page_title" class="form-control mr-2" placeholder="Page Title">
                  </div>

                  <label>Meta Data</label>
                  <div class="input-group mb-3 title_row">
                    <textarea name="metadata" id="metadata" class="form-control" rows="3" placeholder="Enter Meta Data"></textarea>
                  </div>

                  <label>Keywords</label>
                  <div class="input-group mb-3 title_row">
                    <textarea name="keywords" id="keywords" class="form-control" rows="3" placeholder="Enter Keywords"></textarea>
                  </div>
                </div>

                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right" id="post-blog-btn">Submit</button>
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
    post_blog_form = $('#post-blog-form');

    blog_category.select2({
      theme: 'bootstrap4'
    });

    // Summernote
    blog_content.summernote({
      height: 300
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    post_blog_form.submit(function(e){
      this_obj = $(this);
      submit_btn = $('#post-blog-btn');

      e.preventDefault();
      var formData = new FormData(this);

      submit_btn.html('<i class="fa fa-spinner" aria-hidden="true"></i> Submitting...').attr('disabled', true);

      $.ajax({
        dataType: 'json',
        type: 'POST',
        data: formData ,
        url: "{{ route('blog.submit') }}",
        cache: false,
        contentType: false,
        processData: false,
        success:function(data) {
          submit_btn.html('Submit').attr('disabled', false);

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Blog created successfully!');
            post_blog_form[0].reset();
            blog_content.summernote('reset');
          }
        }
      });
    });
    
  });

</script>
@endsection
