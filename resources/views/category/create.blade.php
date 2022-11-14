@extends('layouts.app')

@section('stylesheets')
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
            <h1 class="m-0">Add Category</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
              <li class="breadcrumb-item active">Add New</li>
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
                    <h3 class="card-title">Enter Category Title</h3>
                </div>
                <form id="add-category-form" action="javascript: void(0);">
                    <div class="card-body">
                        <label>Category Title</label>
                        <div class="input-group mb-3 title_row">
                            <input type="text" name="category_name" class="form-control mr-2" placeholder="Category Title">
                        </div>

                        <label>Content</label>
                        <div class="input-group mb-3 title_row">
                          <textarea name="content" id="content"></textarea>
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
                        <button type="submit" class="btn btn-success float-right" id="add-category-submit">Submit</button>
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
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<script>
  $(function () {
    content = $('#content');
    // Summernote
    content.summernote({
      height: 300
    });

    add_service_form = $('#add-category-form');

    //++++++++++++++++++++ SUBMIT FORM :: Start ++++++++++++++++++++//
    add_service_form.submit(function(e){
      add_category_btn = $('#add-category-submit');

      e.preventDefault();
      var formData = new FormData(this);

      add_category_btn.html('<i class="fa fa-spinner" aria-hidden="true"></i> Submitting...').attr('disabled', true);

      //
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        dataType: 'json',
        type: 'POST',
        data: formData,
        url: "{{ route('category.add-submit') }}",
        cache: false,
        contentType: false,
        processData: false,
        success:function(data) {
          add_category_btn.html('Submit').attr('disabled', false);

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Category added successfully!');

            add_service_form[0].reset();
            content.summernote('reset');
          }

          $('.btn').attr('disabled', false);
          add_category_btn.html('Submit');
        }
      });
      //
    });
    //++++++++++++++++++++ SUBMIT FORM :: End ++++++++++++++++++++//
  });

</script>
@endsection
