<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Categories;

use Validator;

class CategoryController extends Controller
{
    protected $statusArray = [
        1 => 'Avtive',
        0 => 'Inactive',
    ];

    public function index(Request $request){
        $categories_qry = Categories::with('blogs');

        if( $request->filled('cat_title') ){
            $categories_qry->where('name', 'like', '%' . $request->cat_title . '%');
        }

        if( $request->filled('cat_status') ){
            $categories_qry->where('status', $request->cat_status);
        }

        $categories = $categories_qry->orderby('id','desc')->paginate(15);

        return view('category.index', compact('categories'))->with([ 'statusArray' => $this->statusArray ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addCategorySubmit(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $validator_array = [];

            $validator_array['category_name'] = 'required|max:255';

            $validator = Validator::make($request->all(), $validator_array);

            $validator_errors = implode('<br>', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json(['status' => 'failed', 'error' => ['message' => $validator_errors]]);
            }

            Categories::create([
                'name' => $request->category_name,
                'slug' => Categories::generateSlug($request->category_name),
                'content' => $request->content ?? null,
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid){
        $category = Categories::with('blogs')->where('uuid', $uuid)->first();

        return view('category.edit', compact('category'));
    }

    public function updateCategorySubmit(Request $request, $uuid){
        $response = [];

        $response['status'] = '';

        try {
            $category_uuid = $uuid ?? '';

            if( $category_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category found!']]);
            }

            $category_name = $request->category_name ? trim($request->category_name) : '';
            $slug_editable = $request->slug_editable ?? 0;
            $category_slug = $request->category_slug ?? '';
            $content = $request->content ?? null;
            $page_title = $request->page_title ?? null;
            $metadata = $request->metadata ?? null;
            $keywords = $request->keywords ?? null;
            $category_status = $request->category_status ?? null;

            if( empty($category_name) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category title found!']]);
            }

            if( empty($category_slug) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category slug found!']]);
            }

            $duplicate_slug = Categories::withTrashed()
                                        ->where('slug', $category_slug)
                                        ->where('uuid', '<>', $category_uuid)
                                        ->count();

            if( $duplicate_slug > 0 ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'This slug can\'t be used!']]);
            }

            if( is_null($category_status) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'Invalid category status']]);
            }

            $category = Categories::with('blogs')->where('uuid', $category_uuid)->first();

            $category->name = $category_name;
            $category->slug = $category_slug;
            $category->content = $content;
            $category->page_title = $page_title;
            $category->metadata = $metadata;
            $category->keywords = $keywords;
            if( $category->blogs->count() == 0 ){
                $category->status = $category_status;
            }
            $category->save();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function regenerateSlug(Request $request){
        $response = [];

        $response['status'] = '';
        $response['category_slug'] = '';

        try {
            $category_name = $request->category_name ?? '';

            if( $category_name == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category title found!']]);
            }

            $response['category_slug'] = Categories::generateSlug($category_name);           
            $response['status'] = 'success';

        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function changeCategoryStatus(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $category_uuid = $request->category_uuid ?? '';

            if( $category_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category found!']]);
            }

            $category = Categories::with('blogs')->where('uuid', $category_uuid)->first();

            if( $category->blogs->count() > 0 ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'You can\'t change the status of this category as this category has one or more blogs.']]);
            }

            $category->status = ($category->status == '1') ? '0' : '1';
            $category->save();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function deleteCategory(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $category_uuid = $request->category_uuid ?? '';

            if( $category_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category found!']]);
            }

            $category = Categories::with('blogs')->where('uuid', $category_uuid)->first();

            if( $category->blogs->count() > 0 ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'You can\'t delete this category as this category has one or more blogs.']]);
            }

            $category->delete();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

}
