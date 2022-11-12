@extends('layouts.app')

@section('stylesheets')
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
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
            <h1 class="m-0">Edit Service Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('service.index') }}">Service</a></li>
              <li class="breadcrumb-item active title_wrap">{{ $service->title }}</li>
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
                    <h3 class="card-title title_wrap">{{ $service->title }}</h3>{{ $service->slug }}
                </div>
                <form id="edit-service-form" action="javascript: void(0);">
                  <div class="card-body">
                    <label>Service Title</label>
                    <div class="input-group mb-3 title_row">
                      <input type="text" name="service_title" class="form-control mr-2" placeholder="Service Title" value="{{ $service->title }}">
                      
                      <div class="icheck-primary d-inline">
                          <input type="checkbox" name="slug_modify" id="slug_modify" value="1" data-toggle="tooltip" data-placement="top" title="Check this box to regenerate the slug.">
                          <label for="slug_modify"></label>
                      </div>
                    </div>

                    <label>service Slug</label>
                    <div class="input-group mb-3 title_row">
                        <input type="text" name="service_slug" class="form-control mr-2" value="{{ $service->slug }}" placeholder="service Slug" disabled>

                        <div class="icheck-primary d-inline">
                            <input type="checkbox" name="slug_editable" id="slug_editable" value="1" data-toggle="tooltip" data-placement="top" title="Check this box to make the slug field editable.">
                            <label for="slug_editable"></label>
                        </div>
                    </div>

                    <label>Select Parent Service</label>
                    <ul>
                      <li class="pt-2 pb-2" style="list-style-type:none;">
                        <div class="icheck-success d-inline mr-5">
                          <input type="radio" name="parent_service" id="parent_service_0" value="0" checked>
                          <label for="parent_service_0" style="font-weight: normal;">No Parent</label>
                        </div>
                      </li>
                        @if (count($servicesTree) > 0)
                          @foreach ($servicesTree as $serviceT)
                            @include('service.tree-dropdown', ['serviceT' => $serviceT, 'p_id' => 0])
                          @endforeach
                        @endif
                    </ul>

                    <label>Content</label>
                    <div class="input-group mb-3 title_row">
                      <textarea name="content" id="content">{{ $service->content }}</textarea>
                    </div>

                    <label>Icon Class</label>
                    <div class="input-group mb-3 title_row">
                      <input type="text" name="icon_class" class="form-control mr-2" value="{{ $service->icon_class }}" placeholder="Icon Class Name">
                    </div>

                    <label>Page Title</label>
                    <div class="input-group mb-3 title_row">
                      <input type="text" name="page_title" class="form-control mr-2" value="{{ $service->page_title }}" placeholder="Page Title">
                    </div>

                    <label>Meta Data</label>
                    <div class="input-group mb-3 title_row">
                      <textarea name="metadata" id="metadata" class="form-control" rows="3" placeholder="Enter Meta Data">{{ $service->metadata }}</textarea>
                    </div>

                    <label>Keywords</label>
                    <div class="input-group mb-3 title_row">
                      <textarea name="keywords" id="keywords" class="form-control" rows="3" placeholder="Enter Keywords">{{ $service->keywords }}</textarea>
                    </div>
                    
                  </div>

                  <!-- /.card-body -->
                  <div class="card-footer">
                      <button type="submit" class="btn btn-success float-right" id="update-service-btn">Update</button>
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

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    slug_modify_field = $('input[name="slug_modify"]');
    service_slug_field = $('input[name="service_slug"]');

    //++++++++++++++++++++ REGENERATE SLUG :: Start ++++++++++++++++++++//
    $('input[name="service_title"]').on('blur', function(){
      if( slug_modify_field.is(':checked') ){
        regenerate_slug();
      }
      else{
        service_slug_field.val('{{ $service->slug }}');
      }
    });

    slug_modify_field.on('change', function(){
      if( $(this).is(':checked') ){
        regenerate_slug();
      }
      else{
        service_slug_field.val('{{ $service->slug }}');
      }
    });
    //++++++++++++++++++++ REGENERATE SLUG :: End ++++++++++++++++++++//

    $('input[name="slug_editable"]').on('change', function(){
      if( $(this).is(':checked') ){
        service_slug_field.prop('disabled', false);
      }
      else{
        service_slug_field.val('{{ $service->slug }}').prop('disabled', true);
      }
    });


    $('#edit-service-form').submit(function(e){
      update_service_btn = $('#update-service-btn');

      e.preventDefault();
      var formData = new FormData(this);
      service_slug = service_slug_field.val().trim();

      update_service_btn.html('<i class="fa fa-spinner" aria-hidden="true"></i> Updating...').attr('disabled', true);

      //
      $.ajax({
        dataType: 'json',
        type: 'POST',
        data: formData,
        url: "{{ route('service.update-submit', $service->uuid) }}",
        cache: false,
        contentType: false,
        processData: false,
        success:function(data) {
          update_service_btn.html('Update').attr('disabled', false);

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Service info updated successfully!');

            window.location.reload();
          }

          $('.btn').attr('disabled', false);
          update_service_btn.html('Update');
        }
      });
      //
    });
    
  });

  function regenerate_slug(){
    service_title = $('input[name="service_title"]').val().trim();

    $.ajax({
      dataType: 'json',
      type: 'POST',
      data:{
        service_title: service_title,
      },
      url: "{{ route('service.regenerate-slug') }}",
      success:function(data) {
        if( data.status == 'failed' ){
          swal_fire_error(data.error.message);
          return false;
        }
        else if( data.status == 'success' ){
          service_slug_field.val(data.regenerated_slug);
        }
      }
    });
  }

</script>
@endsection
