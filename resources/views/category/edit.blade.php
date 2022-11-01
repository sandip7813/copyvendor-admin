@extends('layouts.app')

@section('stylesheets')
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Category Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
              <li class="breadcrumb-item active title_wrap">{{ $category->name }}</li>
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
          <div class="col-md-3"></div>

          <div class="col-md-6">

            <!-- Input addon -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title title_wrap">{{ $category->name }}</h3>
                </div>
                <form id="add-category-form" action="javascript: void(0);">
                    <div class="card-body">
                        <label>Category Title</label>
                        <div class="input-group mb-3 title_row">
                            <input type="text" name="category_name" class="form-control mr-2" value="{{ $category->name }}" placeholder="Category Title">

                            <div class="icheck-primary d-inline">
                                <input type="checkbox" name="slug_modify" id="slug_modify" value="1" data-toggle="tooltip" data-placement="top" title="Check this box to regenerate the slug.">
                                <label for="slug_modify"></label>
                            </div>
                        </div>

                        <label>Category Slug</label>
                        <div class="input-group mb-3 title_row">
                            <input type="text" name="category_slug" class="form-control mr-2" value="{{ $category->slug }}" placeholder="Category Slug" disabled>

                            <div class="icheck-primary d-inline">
                                <input type="checkbox" name="slug_editable" id="slug_editable" value="1" data-toggle="tooltip" data-placement="top" title="Check this box to make the slug field editable.">
                                <label for="slug_editable"></label>
                            </div>
                        </div>

                        <label>Status</label>
                        <div class="input-group mb-3 title_row">
                            <div class="icheck-success d-inline mr-5">
                              <input type="radio" name="category_status" id="status_active" value="1" @if($category->status == '1')checked @endif>
                              <label for="status_active">Active</label>
                            </div>
                            <div class="icheck-danger d-inline">
                              <input type="radio" name="category_status" id="status_inactive" value="0" @if($category->status == '0')checked @endif>
                              <label for="status_inactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-right" id="update-category-submit">Update</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
          </div>
          <!--/.col (left) -->

          <div class="col-md-3"></div>

        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('scripts')
<script>
  $(function () {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    slug_modify_field = $('input[name="slug_modify"]');
    category_slug_field = $('input[name="category_slug"]');

    //++++++++++++++++++++ REGENERATE SLUG :: Start ++++++++++++++++++++//
    $('input[name="category_name"]').on('blur', function(){
      regenerate_slug();
      if( slug_modify_field.is(':checked') ){
        regenerate_slug();
      }
      else{
        category_slug_field.val('{{ $category->slug }}');
      }
    });

    slug_modify_field.on('change', function(){
      if( $(this).is(':checked') ){
        regenerate_slug();
      }
      else{
        category_slug_field.val('{{ $category->slug }}');
      }
    });
    //++++++++++++++++++++ REGENERATE SLUG :: End ++++++++++++++++++++//

    $('input[name="slug_editable"]').on('change', function(){
      //category_slug = $('input[name="category_slug"]');

      if( $(this).is(':checked') ){
        category_slug_field.prop('disabled', false);
      }
      else{
        category_slug_field.val('{{ $category->slug }}').prop('disabled', true);
      }
    });


    $('#update-category-submit').on('click', function(){
      category_name = $('input[name="category_name"]').val().trim();
      slug_editable = $('input[name="slug_editable"]:checked').val();
      category_slug = category_slug_field.val().trim();
      category_status = $('input[name="category_status"]:checked').val();

      if( category_name == '' ){
        swal_fire_error('No category title found!');
        return false;
      }

      if( (slug_editable == 1) && (category_slug == '') ){
        swal_fire_error('No category slug found!');
        return false;
      }

      this_obj = $(this);

      this_obj.html('<i class="fa fa-spinner" aria-hidden="true"></i> Updating...').attr('disabled', true);

      //
      $.ajax({
        dataType: 'json',
        type: 'POST',
        data:{
          category_uuid: '{{ $category->uuid }}',
          category_name: category_name,
          slug_editable: slug_editable,
          category_slug: category_slug,
          category_status: category_status,
        },
        url: "{{ route('category.update-submit') }}",
        success:function(data) {
          this_obj.html('Update').attr('disabled', false);

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Category info updated successfully!');

            $('.title_wrap').html(category_name);
          }

          $('.btn').attr('disabled', false);
          this_obj.html('Submit');
        }
      });
      //
    });
    
  });

  function regenerate_slug(){
    category_name = $('input[name="category_name"]').val().trim();

    $.ajax({
      dataType: 'json',
      type: 'POST',
      data:{
        category_name: category_name,
      },
      url: "{{ route('category.regenerate-slug') }}",
      success:function(data) {
        if( data.status == 'failed' ){
          swal_fire_error(data.error.message);
          return false;
        }
        else if( data.status == 'success' ){
          category_slug_field.val(data.category_slug);
        }
      }
    });
  }

</script>
@endsection
