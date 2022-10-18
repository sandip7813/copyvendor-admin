@extends('layouts.app')

@section('stylesheets')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@include('includes.datatable-css')
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
            <h1 class="m-0">Manage Categories</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Manage Categories</li>
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
                @if( $categories->count() > 0 )
                  <table id="datatable-list" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th class="no-sort">Status</th>
                        <th class="no-sort">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($categories as $cat)
                        <tr>
                          <td>{{ $cat->name }}</td>
                          <td>{{ $cat->slug }}</td>
                          <td>
                            <input type="checkbox" name="cat_status" class="status_toggle" data-onstyle="success" data-offstyle="danger" data-on="Active" data-off="Inactive" data-size="mini" data-width="80" data-uuid="{{ $cat->uuid }}" @if($cat->status == 1) checked @endif data-toggle="toggle">
                          </td>
                          <td>
                            <a href="{{ route('category.edit', $cat->uuid) }}" data-toggle="tooltip" data-placement="top" title="Edit this Category info"><i class="fas fa-edit"></i></a>
                            &nbsp;&nbsp;&nbsp;
                            <a href="javascript: void(0);" data-toggle="tooltip" data-placement="top" title="Delete this Category" class="delete_category" data-uuid="{{ $cat->uuid }}"><i class="fas fa-trash-alt"></i></a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
                  </table>
                @else
                <p>No Category found!</p>
                @endif
              </div>
              <!-- /.card-body -->
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
<script src="{{ asset('js/sweetalert2@11.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

@include('includes.datatable-js')

<script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>

<script>
  $(function () {
    $('#datatable-list').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
      }]
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    //+++++++++++++++++++ UPDATE CATEGORY STATUS :: Start +++++++++++++++++++//
    $(".status_toggle").on('change', function(){
      this_obj = $(this);
      category_uuid = this_obj.data('uuid');

      this_obj.bootstrapToggle('disable');

      $.ajax({
        dataType: 'json',
        type: 'POST',
              data:{
                category_uuid: category_uuid
              },
              url: "{{ route('change-category-status') }}",
              success:function(data) {
                this_obj.bootstrapToggle('enable');

                if( data.status == 'failed' ){
                  swal_fire_error(data.error.message);
                  return false;
                }
                else if( data.status == 'success' ){
                  swal_fire_success('Category status updated successfully!');
                }
              }
      });
    });
    //+++++++++++++++++++ UPDATE CATEGORY STATUS :: End +++++++++++++++++++//

    //+++++++++++++++++++ DELETE CATEGORY :: Start +++++++++++++++++++//
    $('.delete_category').on('click', function(){
      this_obj = $(this);
      category_uuid = this_obj.data('uuid');

      Swal.fire({
        title: 'Do you want to delete this category permanently?',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            dataType: 'json',
            type: 'POST',
                  data:{
                    category_uuid: category_uuid
                  },
                  url: "{{ route('delete-category') }}",
                  success:function(data) {
                    this_obj.bootstrapToggle('enable');

                    if( data.status == 'failed' ){
                      swal_fire_error(data.error.message);
                      return false;
                    }
                    else if( data.status == 'success' ){
                      swal_fire_success('Category deleted successfully!');
                      $(this_obj).parents('tr').fadeOut('slow');
                    }
                  }
          });
        }
      });
    });
    //+++++++++++++++++++ DELETE CATEGORY :: End +++++++++++++++++++//

  });

</script>
@endsection
