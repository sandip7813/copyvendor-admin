@extends('layouts.app')

@section('stylesheets')
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Blogs</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Manage Blogs</li>
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
                
                {{-- +++++++++++++++++++ SEARCH RECORDS :: Start +++++++++++++++++++ --}}
                <div class="card card-info">
                  <div class="card-header">
                    <h3 class="card-title">Search Box</h3>
                  </div>

                  <div class="card-body">
                    <form action="{{ route('blog.index') }}" method="get">
                      <div class="row">
                        <div class="col-md-5">
                          <input type="text" name="blog_title" class="form-control mr-2" placeholder="Blog Title" value="{{ request('blog_title') }}">
                        </div>

                        @if( $categories )
                          <div class="col-md-3">
                            <select name="blog_category" class="form-control select2bs4 select2_dropdown" style="width: 100%;">
                              <option value="">Select Category</option>
                              @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @if( request('blog_category') == $cat->id ) selected @endif>{{ $cat->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        @endif

                        <div class="col-md-2">
                          <select name="blog_status" class="form-control select2bs4 select2_dropdown" style="width: 100%;">
                            <option value="">Select Status</option>
                            @foreach($statusArray as $status_key => $status_val)
                              <option value="{{ $status_key }}" @if( request('blog_status') == $status_key ) selected @endif>{{ $status_val }}</option>
                            @endforeach
                          </select>
                        </div>

                        <div class="col-md-2">
                          <button type="submit" class="btn btn-primary">Search</button> &nbsp;
                          <a href="{{ route('blog.index') }}" class="btn btn-default">Clear</a>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                {{-- +++++++++++++++++++ SEARCH RECORDS :: End +++++++++++++++++++ --}}

                @if( $blogs->count() > 0 )
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Banner</th>
                      <th>Title</th>
                      <th>Slug</th>
                      <th>Category</th>
                      <th class="no-sort">Status</th>
                      <th class="no-sort">Action</th>
                    </tr>
                  </thead>
                    <tbody>
                      @foreach($blogs as $num => $blog)
                        <tr id="tr_{{ $blog->uuid }}">
                          <td>
                            @if( $blog->banner->title )
                              <img src="{{ asset('images/blog_banners/200x160/' . $blog->banner->title) }}" style="width: 100px;">
                            @endif
                          </td>
                          <td>{{ $blog->title }}</td>
                          <td>{{ $blog->slug }}</td>
                          <td>{{ $blog->category->name ?? '' }}</td>
                          <td>
                            <input type="checkbox" name="blog_status" class="status_toggle" data-onstyle="success" data-offstyle="danger" data-on="Active" data-off="Inactive" data-size="mini" data-width="80" data-uuid="{{ $blog->uuid }}" @if($blog->status == 1) checked @endif data-toggle="toggle">
                          </td>
                          <td>
                            <div>
                              <a href="{{ route('blog.edit', $blog->uuid) }}" data-toggle="tooltip" data-placement="top" title="Edit this Blog info"><i class="fas fa-edit"></i></a>
                              &nbsp;&nbsp;&nbsp;
                              <a href="javascript: void(0);" data-toggle="tooltip" data-placement="top" title="Change the Banner Image" class="open_banner_upload_modal" data-uuid="{{ $blog->uuid }}" data-num="{{ $num }}"><i class="fas fa-upload"></i></a>
                              &nbsp;&nbsp;&nbsp;
                              <a href="javascript: void(0);" data-toggle="tooltip" data-placement="top" title="Delete this Blog" class="delete_blog" data-uuid="{{ $blog->uuid }}"><i class="fas fa-trash-alt"></i></a>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  <tfoot>
                    <tr>
                      <th>Banner</th>
                      <th>Title</th>
                      <th>Slug</th>
                      <th>Category</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                </table>
                @else
                  <p>No blog found!</p>
                @endif
              </div>
              <!-- /.card-body -->

              <div class="card-footer clearfix">
                <div class="pagination-sm m-0 float-right">
                  {{ $blogs->withQueryString()->links() }}
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

  <div class="modal fade" id="change_banner_modal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="change-banner-form" action="javascript: void(0);" enctype="multipart/form-data">
          <div class="overlay" id="banner_modal_overlay">
              <i class="fas fa-2x fa-sync fa-spin"></i>
          </div>
          <div class="modal-header">
            <h4 class="modal-title" id="banner_modal_title">...</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="banner_modal_image">...</div>
            <div class="card card-info mt-3">
              <div class="card-body">
                  <input type="hidden" name="blog_modal_uuid">
                  <div class="row">
                    <input class="form-control" type="file" name="banner">
                  </div>
                
              </div>
            </div>

          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Upload Banner</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
@endsection

@section('scripts')
<script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script>
  $(function () {
    var blogs = @json($blogs ? $blogs->toArray() : []);

    select2_dropdown = $('.select2_dropdown');
    change_banner_modal = $('#change_banner_modal');
    change_banner_form = $('#change-banner-form');
    banner_modal_overlay = $('#banner_modal_overlay');

    select2_dropdown.select2({
      theme: 'bootstrap4'
    });

    //+++++++++++++++++++ OPEN BANNER UPLOAD MODAL :: Start +++++++++++++++++++//
    $('.open_banner_upload_modal').on('click', function(){
      banner_modal_overlay.show();

      this_obj = $(this);
      blog_uuid = this_obj.data('uuid');
      blog_num = this_obj.data('num');

      blog = blogs.data[blog_num];
      banner_image = $('<img />').attr('src', "{{ asset('images/blog_banners/1000x600/') }}/" + blog.banner.title).css('max-width', '750px');
      $('input[name="blog_modal_uuid"]').val(blog_uuid);

      change_banner_modal.modal({ show: true, backdrop: 'static', keyboard: false });

      $('#banner_modal_title').html(blog.title);
      $('#banner_modal_image').html(banner_image);

      banner_modal_overlay.hide();
    });
    //+++++++++++++++++++ OPEN BANNER UPLOAD MODAL :: End +++++++++++++++++++//

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    //+++++++++++++++++++ UPDATE BLOG STATUS :: Start +++++++++++++++++++//
    $('.status_toggle').on('change', function(){
      this_obj = $(this);
      blog_uuid = this_obj.data('uuid');

      this_obj.bootstrapToggle('disable');

      $.ajax({
        dataType: 'json',
        type: 'POST',
        data:{
          blog_uuid: blog_uuid
        },
        url: "{{ route('blog.change-status') }}",
        success:function(data) {
          this_obj.bootstrapToggle('enable');

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Blog status updated successfully!');
          }
        }
      });
    });
    //+++++++++++++++++++ UPDATE BLOG STATUS :: End +++++++++++++++++++//

    //+++++++++++++++++++ DELETE BLOG :: Start +++++++++++++++++++//
    $('.delete_blog').on('click', function(){
      this_obj = $(this);
      blog_uuid = this_obj.data('uuid');

      Swal.fire({
        title: 'Do you want to delete this blog permanently?',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            dataType: 'json',
            type: 'POST',
            data:{
              blog_uuid: blog_uuid
            },
            url: "{{ route('blog.delete-item') }}",
            success:function(data) {
              this_obj.bootstrapToggle('enable');

              if( data.status == 'failed' ){
                swal_fire_error(data.error.message);
                return false;
              }
              else if( data.status == 'success' ){
                swal_fire_success('Blog deleted successfully!');
                $(this_obj).parents('tr').fadeOut('slow');
              }
            }
          });
        }
      });
    });
    //+++++++++++++++++++ DELETE BLOG :: End +++++++++++++++++++//

    //+++++++++++++++++++ CHANGE BANNER IMAGE :: Start +++++++++++++++++++//
    change_banner_form.submit(function(e){
      this_obj = $(this);

      e.preventDefault();
      var formData = new FormData(this);
      blog_uuid = formData.get('blog_modal_uuid');

      banner_modal_overlay.show();

      $.ajax({
        dataType: 'json',
        type: 'POST',
        data: formData ,
        url: "{{ route('blog.change-banner') }}",
        cache: false,
        contentType: false,
        processData: false,
        success:function(data) {
          banner_modal_overlay.hide();

          if( data.status == 'failed' ){
            swal_fire_error(data.error.message);
            return false;
          }
          else if( data.status == 'success' ){
            swal_fire_success('Banner changed successfully!');
            change_banner_form[0].reset();

            banner_image = $('<img />').attr('src', "{{ asset('images/blog_banners/1000x600/') }}/" + data.banner_title).css('max-width', '750px');
            $('#banner_modal_image').html(banner_image);

            thumbnail_image = $('<img />').attr('src', "{{ asset('images/blog_banners/200x160/') }}/" + data.banner_title).css('width', '100px');
            $('#tr_' + blog_uuid + ' td:first').html(thumbnail_image);
          }
        }
      });
    });
    //+++++++++++++++++++ CHANGE BANNER IMAGE :: End +++++++++++++++++++//

  });

</script>
@endsection
