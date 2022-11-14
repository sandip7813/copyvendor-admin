
<div class="222">
  <table class="table table-hover">
    <tbody>
      <tr data-widget="expandable-table" aria-expanded="false">
        <td class=" border-top-0 @if (count($service['children']) == 0) border-bottom @endif  mb-0">
          <div class="row">
            <div class="col-10">@if($call_via == 'root') <i class="fas fa-angle-right"></i> @endif @if($call_via == 'child') {{ $cnt }} ) @endif {{ $service['title'] }}</div>
            <div class="col-2">
              @if( count($service['children']) == 0 )
                <input type="checkbox" name="service_status" class="status_toggle" data-onstyle="success" data-offstyle="danger" data-on="Active" data-off="Inactive" data-size="mini" data-width="80" data-uuid="{{ $service['uuid'] }}" @if($service['status'] == 1) checked @endif data-toggle="toggle">
              &nbsp;&nbsp;&nbsp;
              @endif
              <a href="{{ route('service.edit', $service['uuid']) }}" data-uuid="{{ $service['uuid'] }}" data-toggle="tooltip" data-placement="top" title="Edit this Service info" @if( count($service['children']) > 0 ) class="float-right" @endif><i class="fas fa-edit"></i></a>
              @if( count($service['children']) == 0 )
              &nbsp;&nbsp;&nbsp;
                <a href="javascript: void(0);" data-uuid="{{ $service['uuid'] }}" data-toggle="tooltip" data-placement="top" title="Delete this Service" class="delete_service"><i class="fas fa-trash-alt"></i></a>
              @endif
            </div>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>


@if (count($service['children']) > 0)
  <div class="pb-0 mb-0 ml-3">
    <table class="table table-hover">
      <tbody>
        <tr data-widget="expandable-body">
          <td>
            @php $cnt = 1; @endphp
            @foreach($service['children'] as $service)
              @include('service.tree-index', ['service' => $service, 'call_via' => 'child', 'cnt' => $cnt]) 
              @php $cnt++; @endphp
            @endforeach
          </td>
        </tr>
      </tbody>
    </table>
  </div>
@endif