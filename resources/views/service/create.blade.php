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
            <h1 class="m-0">Add Service</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('service.index') }}">Services</a></li>
              <li class="breadcrumb-item active">Add Service</li>
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
                  <h3 class="card-title">Add New Service</h3>
              </div>
              <form id="add-service-form" action="javascript: void(0);">
                <div class="card-body">
                  <label>Service Title</label>
                  <div class="input-group mb-3 title_row">
                    <input type="text" name="service_title" class="form-control mr-2" placeholder="Service Title">
                  </div>

                  <div class="mb-5 mt-2 title_row">
                    <label>Select Parent Service</label>
                    <ul>
                      <li class="pt-2 pb-2" style="list-style-type:none;">
                        <div class="icheck-success d-inline mr-5">
                          <input type="radio" name="parent_service" id="parent_service_0" value="0">
                          <label for="parent_service_0" style="font-weight: normal;">No Parent</label>
                        </div>
                      </li>
                        @if (count($servicesTree) > 0)
                          @foreach ($servicesTree as $serviceT)
                            @include('service.tree-dropdown', ['serviceT' => $serviceT, 'p_id' => 0])
                          @endforeach
                        @endif
                    </ul>
                  </div>

                  <label>Content</label>
                  <div class="input-group mb-3 title_row">
                    <textarea name="content" id="content"></textarea>
                  </div>

                  <label>Icon Class</label>
                  <div class="input-group mb-3 title_row">
                    <input type="text" name="icon_class" class="form-control mr-2" placeholder="Icon Class Name">
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
                    <button type="submit" class="btn btn-success float-right" id="add-service-btn">Submit</button>
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
    add_service_form = $('#add-service-form');
    add_service_btn = $('#add-service-btn');

    content = $('#content');

    // Summernote
    content.summernote({
      height: 300
    });

    $('#parent_service').select2({
      theme: 'bootstrap4'
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    add_service_form.submit(function(e){
      add_service_btn.html('<i class="fa fa-spinner" aria-hidden="true"></i> Submitting...').attr('disabled', true);

      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
        dataType: 'json',
        type: 'POST',
        data: formData,
        url: "{{ route('service.submit') }}",
        cache: false,
        contentType: false,
        processData: false,
        success:function(data) {
          add_service_btn.html('Submit').attr('disabled', false);

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Service added successfully!');
            add_service_form[0].reset();
            content.summernote('reset');
            $('#parent_service').val(0).trigger('change');
          }
        }
      });
    });
    
  });

</script>
@endsection
