<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Categories;
use App\Models\Blogs;
use App\Models\Images;

use Illuminate\Support\Str;
use Validator;
use Image;
use Illuminate\Support\Facades\File; 

class BlogController extends Controller
{
    public $categories;

    protected $statusArray = [
        1 => 'Avtive',
        0 => 'Inactive',
    ];

    public function __construct(){
        $this->categories = Categories::orderBy('name', 'asc')->get();
    }

    public function index(Request $request){
        $blogs_qry = Blogs::with(['category', 'banner']);

        if( $request->filled('blog_title') ){
            $blogs_qry->where('title', 'like', '%' . $request->blog_title . '%');
        }

        if( $request->filled('blog_category') ){
            $blogs_qry->where('category_id', $request->blog_category);
        }

        if( $request->filled('blog_status') ){
            $blogs_qry->where('status', $request->blog_status);
        }

        $blogs = $blogs_qry->orderby('id','desc')->paginate(10);

        return view('blog.index')->with([
                                        'blogs' => $blogs, 
                                        'categories' => $this->categories,
                                        'statusArray' => $this->statusArray
                                    ]);
    }

    public function create(){
        return view('blog.create')->with(['categories' => $this->categories]);
    }

    public function blogSubmit(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $validator_array = [];

            $validator_array['blog_title'] = 'required|max:255';
            $validator_array['blog_category'] = 'required';
            $validator_array['blog_content'] = 'required';
            $validator_array['banner'] = 'required|mimes:jpeg,jpg,png,gif|max:10000';

            $validator = Validator::make($request->all(), $validator_array);

            $validator_errors = implode('<br>', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json(['status' => 'failed', 'error' => ['message' => $validator_errors]]);
            }

            //+++++++++++++++++++++++++++ STORE & CROP IMAGES :: Start +++++++++++++++++++++++++++//
            if($request->hasFile('banner')) {
                $banner = Image::make($request->file('banner'));

                $bannerName = time() . '-' . uniqid() . '.' . $request->file('banner')->getClientOriginalExtension();
                $bannerDir = 'images/blog_banners/';

                //------------- MAIN BANNER UPLOAD :: Start -------------//
                $destinationPath = public_path( $bannerDir . 'main/' );
                $banner->save($destinationPath . $bannerName);
                //------------- MAIN BANNER UPLOAD :: End -------------//

                //------------- 1000 x 600 BANNER UPLOAD :: Start -------------//
                $destinationPathThumbnail = public_path( $bannerDir . '1000x600/' );
                $banner->resize(1000, 600);
                $banner->save($destinationPathThumbnail . $bannerName);
                //------------- 1000 x 600 BANNER UPLOAD :: End -------------//
    
                //------------- 200 x 160 BANNER UPLOAD :: Start -------------//
                $destinationPathThumbnail = public_path( $bannerDir . '200x160/' );
                $banner->resize(200, 160);
                $banner->save($destinationPathThumbnail . $bannerName);
                //------------- 200 x 160 BANNER UPLOAD :: End -------------//
            }
            //+++++++++++++++++++++++++++ STORE & CROP IMAGES :: End +++++++++++++++++++++++++++//

            $blog = Blogs::create([
                        'category_id' => $request->blog_category,
                        'title' => $request->blog_title,
                        'slug' => Blogs::generateSlug($request->blog_title),
                        'content' => $request->blog_content,
                        'page_title' => $request->page_title ?? null,
                        'metadata' => $request->metadata ?? null,
                        'keywords' => $request->keywords ?? null,
                    ]);
            
            $banner = Images::create([
                'type' => 'blog_banner',
                'item_id' => $blog->id,
                'title' => $bannerName
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
            $blog_uuid = $request->blog_uuid ?? '';

            if( $blog_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No blog found!']]);
            }

            $blog = Blogs::where('uuid', $blog_uuid)->first();

            $blog->status = ($blog->status == '1') ? '0' : '1';
            $blog->save();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function changeBanner(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $validator_array = [];

            $validator_array['blog_modal_uuid'] = 'required';
            $validator_array['banner'] = 'required|mimes:jpeg,jpg,png,gif|max:10000';

            $validator = Validator::make($request->all(), $validator_array);

            $validator_errors = implode('<br>', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json(['status' => 'failed', 'error' => ['message' => $validator_errors]]);
            }

            //+++++++++++++++++++++++++++ STORE & CROP IMAGES :: Start +++++++++++++++++++++++++++//
            if($request->hasFile('banner')) {
                $blog_uuid = $request->blog_modal_uuid ?? '';

                $blog = Blogs::with('banner')->where('uuid', $blog_uuid)->first();

                if ( !isset($blog->id) ) {
                    return response()->json(['status' => 'failed', 'error' => ['message' => 'No blog found!']]);
                }

                $bannerDir = 'images/blog_banners/';

                //------------- DELETE EXISTING IMAGES :: Start -------------//
                $existingBanner = $blog->banner->title ?? null;

                if( !is_null($existingBanner) ){
                    File::delete( $bannerDir . 'main/' . $existingBanner );
                    File::delete( $bannerDir . '1000x600/' . $existingBanner );
                    File::delete( $bannerDir . '200x160/' . $existingBanner );
                }
                //------------- DELETE EXISTING IMAGES :: End -------------//

                $banner = Image::make($request->file('banner'));

                $bannerName = time() . '-' . uniqid() . '.' . $request->file('banner')->getClientOriginalExtension();

                //------------- MAIN BANNER UPLOAD :: Start -------------//
                $destinationPath = public_path( $bannerDir . 'main/' );
                $banner->save($destinationPath . $bannerName);
                //------------- MAIN BANNER UPLOAD :: End -------------//

                //------------- 1000 x 600 BANNER UPLOAD :: Start -------------//
                $destinationPathThumbnail = public_path( $bannerDir . '1000x600/' );
                $banner->resize(1000, 600);
                $banner->save($destinationPathThumbnail . $bannerName);
                //------------- 1000 x 600 BANNER UPLOAD :: End -------------//
    
                //------------- 200 x 160 BANNER UPLOAD :: Start -------------//
                $destinationPathThumbnail = public_path( $bannerDir . '200x160/' );
                $banner->resize(200, 160);
                $banner->save($destinationPathThumbnail . $bannerName);
                //------------- 200 x 160 BANNER UPLOAD :: End -------------//

                $blog->banner->title = $bannerName;
                $blog->banner->save();

                $response['banner_title'] = $bannerName;
                $response['status'] = 'success';
            }
            //+++++++++++++++++++++++++++ STORE & CROP IMAGES :: End +++++++++++++++++++++++++++//

        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function deleteBlog(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $blog_uuid = $request->blog_uuid ?? '';

            if( $blog_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No blog found!']]);
            }

            $blog = Blogs::where('uuid', $blog_uuid)->first();
            $blog->delete();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function edit($uuid){
        $blog = Blogs::where('uuid', $uuid)->first();

        return view('blog.edit', compact('blog'))->with(['categories' => $this->categories]);
    }

    public function regenerateSlug(Request $request){
        $response = [];

        $response['status'] = '';
        $response['blog_slug'] = '';

        try {
            $blog_title = $request->blog_title ?? '';

            if( $blog_title == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No blog title found!']]);
            }

            $response['blog_slug'] = Blogs::generateSlug($blog_title);           
            $response['status'] = 'success';

        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }

    public function updateBlogSubmit(Request $request){
        $response = [];

        $response['status'] = '';

        try {
            $blog_uuid = $request->blog_uuid ?? '';

            if( $blog_uuid == '' ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No blog found!']]);
            }

            $blog_title = $request->blog_title ?? '';
            $slug_editable = $request->slug_editable ?? 0;
            $blog_slug = $request->blog_slug ?? '';
            $blog_category = $request->blog_category ?? '';
            $blog_content = $request->blog_content ?? '';
            $page_title = $request->page_title ?? NULL;
            $metadata = $request->metadata ?? NULL;
            $keywords = $request->keywords ?? NULL;
            $blog_status = $request->blog_status ?? null;

            if( empty($blog_title) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No blog title found!']]);
            }

            if( empty($blog_slug) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'No blog slug found!']]);
            }

            $duplicate_slug = Blogs::withTrashed()
                                    ->where('slug', $blog_slug)
                                    ->where('uuid', '<>', $blog_uuid)
                                    ->count();

            if( $duplicate_slug > 0 ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'This slug can\'t be used!']]);
            }

            if( is_null($blog_status) ){
                return response()->json(['status' => 'failed', 'error' => ['message' => 'Invalid blog status']]);
            }

            $blog = Blogs::where('uuid', $blog_uuid)->first();

            $blog->title = $blog_title;
            $blog->slug = $blog_slug;
            $blog->category_id = $blog_category;
            $blog->content = $blog_content;
            $blog->page_title = $page_title;
            $blog->metadata = $metadata;
            $blog->keywords = $keywords;
            $blog->status = $blog_status;
            $blog->save();            

            $response['status'] = 'success';
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'failed', 'error' => ['message' => $e->getMessage()], 'e' => $e]);
        }

        return response()->json($response);
    }
}
