@extends('layouts.app')

@section('stylesheets')
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Services</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Manage Services</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">

                <!-- /.row -->
                <div class="row">
                  <div class="col-12">
                    <div class="card">

                      <div class="card-header">
                        <h3 class="card-title">Services Tree</h3>
                      </div>
                      <div class=" pb-0 mb-0">
                        @if( count($servicesTree) > 0 )
                          <table class="table table-hover 111">
                            <tbody>
                              <tr data-widget="expandable-body">
                                <td class="">
                                  @php $cnt = 0; @endphp
                                  @foreach ($servicesTree as $service)
                                    @include('service.tree-index', ['service' => $service, 'call_via' => 'root', 'cnt' => $cnt])
                                  @endforeach
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>

            </div>
            <!-- /.card -->

          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

  </div>
  <!-- /.content-wrapper -->
@endsection

@section('scripts')
<script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
<script>
  $(function () {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    //+++++++++++++++++++ UPDATE SERVICE STATUS :: Start +++++++++++++++++++//
    $('.status_toggle').on('change', function(e){
      e.preventDefault();
      this_obj = $(this);
      status_val = this_obj.prop('checked') ? 1 : 0;

      service_uuid = this_obj.data('uuid');

      this_obj.bootstrapToggle('destroy');

      $.ajax({
        dataType: 'json',
        type: 'POST',
        data:{
          service_uuid: service_uuid,
          status_val: status_val,
        },
        url: "{{ route('service.change-status') }}",
        success:function(data) {
          this_obj.bootstrapToggle('enable');

          console.log('1) ' + data.status);

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);

            toggle_status = (status_val == 0) ? 'on' : 'off';
            this_obj.bootstrapToggle(toggle_status);

            console.log('2) ' + data.status);

            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Service status updated successfully!');
          }
        }
      });
    });
    //+++++++++++++++++++ UPDATE SERVICE STATUS :: End +++++++++++++++++++//

    //+++++++++++++++++++ DELETE SERVICE :: Start +++++++++++++++++++//
    $('.delete_service').on('click', function(){
      this_obj = $(this);
      service_uuid = this_obj.data('uuid');

      Swal.fire({
        title: 'Do you want to delete this service permanently?',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            dataType: 'json',
            type: 'POST',
            data:{
              service_uuid: service_uuid
            },
            url: "{{ route('service.delete-item') }}",
            success:function(data) {
              this_obj.bootstrapToggle('enable');

              if( data.status == 'failed' ){
                swal_fire_error(data.error.message);
                return false;
              }
              else if( data.status == 'success' ){
                swal_fire_success('Service deleted successfully!');
                location.reload();
              }
            }
          });
        }
      });
    });
    //+++++++++++++++++++ DELETE SERVICE :: End +++++++++++++++++++//
  });

</script>
@endsection
