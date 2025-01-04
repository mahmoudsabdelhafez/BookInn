<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Comment;

class BlogController extends Controller
{
    public function BlogCategory(){
        $category = BlogCategory::latest()->get();
        return view('backend.category.blog_category',compact('category'));
    }// End Method 


    public function StoreBlogCategory(Request $request){
        $request->validate([
            'category_name' => 'required|string|max:255|unique:blog_categories,category_name,' . $request->cat_id,
        ]);

        BlogCategory::insert([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
        ]);
        $notification = array(
            'message' => 'BlogCategory Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
         
         
    }// End Method 


    public function EditBlogCategory($id){ // show edit box
        $categories = BlogCategory::find($id);
        return response()->json($categories); // pass the json data to ajax to pass it to the modal
    }// End Method 



    public function UpdateBlogCategory(Request $request){
        $request->validate([
            'category_name' => 'required|string|max:255|unique:blog_categories,category_name,' . $request->cat_id,
        ]);

        $cat_id = $request->cat_id;

        
        BlogCategory::find($cat_id)->update([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
        ]);
        $notification = array(
            'message' => 'BlogCategory Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
         
         
    }// End Method 
    public function DeleteBlogCategory($id){
        BlogCategory::find($id)->delete();
        $notification = array(
            'message' => 'BlogCategory Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }// End Method
    
    
     /////////// All Blog Post Methods////////////////////
     public function AllBlogPost(){
        $post = BlogPost::latest()->get();
        return view('backend.post.all_post',compact('post'));
    }// End Method 

    public function AddBlogPost(){
        $blogcat = BlogCategory::latest()->get();
        return view('backend.post.add_post', compact('blogcat'));
    }

    public function StoreBlogPost(Request $request){

        $request->validate([
            'blogcat_id' => 'required|exists:blog_categories,id',
            'post_title' => 'required|string|max:255|unique:blog_posts,post_title',
            'short_desc' => 'required|string|max:500',
            'long_desc' => 'required|string',
            'post_image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        
        $image = $request->file('post_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(550,370)->save('upload/post/'.$name_gen);
        $save_url = 'upload/post/'.$name_gen;
        BlogPost::insert([
            'blogcat_id' => $request->blogcat_id,
            'user_id' => Auth::user()->id,
            'post_title' => $request->post_title,
            'post_slug' => strtolower(str_replace(' ','-',$request->post_title)),
            'short_desc' => $request->short_desc,
            'long_desc' => $request->long_desc,
            'post_image' => $save_url,
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Blog Post Data Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.blog.post')->with($notification);
    } // End Method 


    public function EditBlogPost($id){
        $blogcat = BlogCategory::latest()->get();
        $post = BlogPost::find($id);
        return view('backend.post.edit_post',compact('blogcat','post'));
    }// End Method 


    public function UpdateBlogPost(Request $request){

        $request->validate([
            'blogcat_id' => 'required|exists:blog_categories,id',
            'post_title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'post_title')->ignore($request->id),
            ],
            'short_desc' => 'required|string|max:500',
            'long_desc' => 'required|string',
            'post_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);


        $post_id = $request->id;
        if($request->file('post_image')){
        $image = $request->file('post_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(550,370)->save('upload/post/'.$name_gen);
        $save_url = 'upload/post/'.$name_gen;
    
        BlogPost::findOrFail($post_id)->update([
    
            'blogcat_id' => $request->blogcat_id,
            'user_id' => Auth::user()->id,
            'post_title' => $request->post_title,
            'post_slug' => strtolower(str_replace(' ','-',$request->post_title)),
            'short_desc' => $request->short_desc,
            'long_desc' => $request->long_desc,
            'post_image' => $save_url,
            'created_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'BlogPost Updated With Image Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.blog.post')->with($notification);
        } else {
            BlogPost::findOrFail($post_id)->update([
    
                'blogcat_id' => $request->blogcat_id,
                'user_id' => Auth::user()->id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ','-',$request->post_title)),
                'short_desc' => $request->short_desc,
                'long_desc' => $request->long_desc, 
                'created_at' => Carbon::now(),
                ]);
        
                $notification = array(
                    'message' => 'BlogPost Updated Without Image Successfully',
                    'alert-type' => 'success'
                );
        
                return redirect()->route('all.blog.post')->with($notification);
        } // End Else 
    }// End Method 
    public function DeleteBlogPost($id){
        $item = BlogPost::findOrFail($id);
        $img = $item->post_image;
        unlink($img);
        BlogPost::findOrFail($id)->delete();
        $notification = array(
            'message' => 'BlogPost Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
     }   // End Method



     // ====================== Front End Blog Post ==================== //

     public function BlogDetails($slug){ // here we pass slug instead of id 
        $blog = BlogPost::where('post_slug',$slug)->first();
        $bcategory = BlogCategory::latest()->get();
        $lpost = BlogPost::latest()->limit(3)->get();
        $comment = Comment::where('post_id', $blog->id)->where('status', '1')->limit(5)->get();

        
        return view('frontend.blog.blog_details',compact('blog','bcategory','lpost','comment'));
     }// End Method 


     public function BlogCatList($id){
        $blog = BlogPost::where('blogcat_id',$id)->get();
        $namecat = BlogCategory::where('id',$id)->first();
        $bcategory = BlogCategory::latest()->get();
        $lpost = BlogPost::latest()->limit(3)->get();
        return view('frontend.blog.blog_cat_list',compact('blog','bcategory','lpost','namecat'));
     }// End Method 


     public function BlogList(){
        $blog = BlogPost::latest()->paginate(3);
        $bcategory = BlogCategory::latest()->get();
        $lpost = BlogPost::latest()->limit(3)->get();
        return view('frontend.blog.blog_all',compact('blog','bcategory','lpost'));     
    }// End Method 






}
