<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Categories;

use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::all();
        return view('category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addCategorySubmit(Request $request)
    {
        $response = [];

        $response['status'] = '';

        try {
            $category_title = $request->category_title ?? [];

            //+++++++++++++++++++++++++ VALIDATION :: Start +++++++++++++++++++++++++//
            if( empty($category_title) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category title found!']]);
            }

            if( !empty($category_title) ){
                $cat_val_exists = 0;

                foreach($category_title as $cat){
                    if( trim($cat) != '' ){
                        $cat_val_exists++;
                    }
                }

                if( $cat_val_exists == 0 ){
                    return response()->json(['status' => 'failed', 'error' => ['message' => 'No category title found!']]);
                }
            }
            //+++++++++++++++++++++++++ VALIDATION :: End +++++++++++++++++++++++++//

            if( count($category_title) > 0 ){
                foreach($category_title as $cat){
                    if( trim($cat) != '' ){
                        Categories::create([
                            'uuid' => (string) Str::uuid(),
                            'name' => $cat,
                            'slug' => Categories::generateSlug($cat),
                            'type' => 'blog'
                        ]);
                    }
                }
            }

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
    public function edit($uuid)
    {
        $category = Categories::where('uuid', $uuid)->first();

        return view('category.edit', compact('category'));
    }

    public function updateCategorySubmit(Request $request)
    {
        $response = [];

        $response['status'] = '';

        try {
            $category_uuid = $request->category_uuid ?? '';

            if( $category_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category found!']]);
            }

            $category_name = $request->category_name ?? '';
            $slug_editable = $request->slug_editable ?? 0;
            $category_slug = $request->category_slug ?? '';
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

            $category = Categories::where('uuid', $category_uuid)->first();

            $category->name = $category_name;
            $category->slug = $category_slug;
            $category->status = $category_status;
            $category->save();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function regenerateSlug(Request $request)
    {
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

    public function changeCategoryStatus(Request $request)
    {
        $response = [];

        $response['status'] = '';

        try {
            $category_uuid = $request->category_uuid ?? '';

            if( $category_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category found!']]);
            }

            $category = Categories::where('uuid', $category_uuid)->first();

            $category->status = ($category->status == '1') ? '0' : '1';
            $category->save();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function deleteCategory(Request $request)
    {
        $response = [];

        $response['status'] = '';

        try {
            $category_uuid = $request->category_uuid ?? '';

            if( $category_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No category found!']]);
            }

            $category = Categories::where('uuid', $category_uuid)->first();
            $category->delete();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

}
