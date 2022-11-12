<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Services;

use Validator;

class ServiceController extends Controller
{
    public function index(){
        $servicesTree = self::servicesTree();

        return view('service.index', compact('servicesTree'));
    }
    
    public function create(){
        $servicesTree = self::servicesTree();

        return view('service.create', compact('servicesTree'));
    }

    public function serviceSubmit(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $validator_array = [];

            $validator_array['service_title'] = 'required|max:255';

            $validator = Validator::make($request->all(), $validator_array);

            $validator_errors = implode('<br>', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json(['status' => 'failed', 'error' => ['message' => $validator_errors]]);
            }

            Services::create([
                        'title' => $request->service_title,
                        'slug' => Services::generateSlug($request->service_title),
                        'parent_id' => ($request->parent_service > 0) ? $request->parent_service : NULL,
                        'content' => $request->content ?? null,
                        'icon_class' => $request->icon_class,
                        'page_title' => $request->page_title ?? null,
                        'metadata' => $request->metadata ?? null,
                        'keywords' => $request->keywords ?? null,
                    ]);

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function changeStatus(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $service_uuid = $request->service_uuid ?? '';
            $status_val = $request->status_val ?? 0;

            if( $service_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No service found!']]);
            }

            $service = Services::where('uuid', $service_uuid)->first();
            $children_count = $service->children()->get()->count();

            if( ($status_val == 0) && ($children_count > 0) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'You can\'t make this Service inactive as this service has ' . $children_count . ' sub-service(s).']]);
            }

            $service = Services::where('uuid', $service_uuid)->first();

            $service->status = ($service->status == '1') ? '0' : '1';
            $service->save();

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function deleteService(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $service_uuid = $request->service_uuid ?? '';

            if( $service_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No Service found!']]);
            }

            $service = Services::where('uuid', $service_uuid)->first();
            $service->delete();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function edit($uuid){
        $service = Services::where('uuid', $uuid)->first();

        $servicesTree = self::servicesTree($uuid);

        return view('service.edit', compact('service', 'servicesTree'));
    }

    public function regenerateSlug(Request $request){
        $response = [];

        $response['status'] = '';
        $response['regenerated_slug'] = '';

        try {
            $service_title = $request->service_title ?? '';

            if( $service_title == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No service title found!']]);
            }

            $response['regenerated_slug'] = Services::generateSlug($service_title);           
            $response['status'] = 'success';

        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function updateServiceSubmit(Request $request, $uuid){
        $response = [];

        $response['status'] = '';

        try {
            $service_uuid = $uuid ?? '';

            if( $service_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No service found!']]);
            }

            $service_title = $request->service_title ?? '';
            $slug_editable = $request->slug_editable ?? 0;
            $service_slug = $request->service_slug ?? NULL;
            $parent_service = $request->parent_service ?? NULL;
            $content = $request->content ?? '';
            $icon_class = $request->icon_class ?? NULL;
            $page_title = $request->page_title ?? NULL;
            $metadata = $request->metadata ?? NULL;
            $keywords = $request->keywords ?? NULL;

            if( empty($service_title) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No service title found!']]);
            }

            $duplicate_slug = Services::withTrashed()
                                        ->where('slug', $service_slug)
                                        ->where('uuid', '<>', $service_uuid)
                                        ->count();

            if( $duplicate_slug > 0 ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'This slug can\'t be used!']]);
            }

            $service = Services::where('uuid', $service_uuid)->first();

            $service->title = $service_title;
            if( !is_null($service_slug) ){
                $service->slug = $service_slug;
            }
            $service->parent_id = ($parent_service > 0) ? $parent_service : NULL;
            $service->content = $content;
            $service->icon_class = $icon_class;
            $service->page_title = $page_title;
            $service->metadata = $metadata;
            $service->keywords = $keywords;
            $service->save();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    private static function servicesTree($explode_uuid = null){
        if( !is_null($explode_uuid) ){
            return Services::where('uuid', '<>', $explode_uuid)->tree()->get()->toTree();
        }
        else{
            return Services::tree()->get()->toTree();
        }
    }
}
