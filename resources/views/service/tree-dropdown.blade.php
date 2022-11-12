<li class="pt-2 pb-2" style="list-style-type:none;">
  <div class="icheck-success d-inline mr-5">
    <input type="radio" name="parent_service" id="parent_service_{{ $serviceT['id'] }}" value="{{ $serviceT['id'] }}" @if($p_id == $serviceT['id']) checked @endif>
    <label for="parent_service_{{ $serviceT['id'] }}" style="font-weight: normal;">{{ $serviceT['title'] }}</label>
  </div>
</li>
@if (count($serviceT['children']) > 0)
  <ul>
  @foreach($serviceT['children'] as $serviceT)
    @include('service.tree-dropdown', $serviceT)
  @endforeach
  </ul>
@endif