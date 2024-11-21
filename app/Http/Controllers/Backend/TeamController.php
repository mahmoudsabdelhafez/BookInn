<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\BookArea;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class TeamController extends Controller
{
    public function AllTeam(){
        $team = Team::latest()->get();
        return view('backend.team.all_team',compact('team'));
    }

    public function AddTeam(){
        return view('backend.team.add_team');
    }

    public function StoreTeam(Request $request ){
            // take in image from form and upload it to "upload/team" folder:
            $image = $request->file('image'); // take an image file from form
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension(); // generate unique name for image e.g. 123.jpg (id+extension)
            Image::make($image)->resize(550,670)->save('upload/team/'.$name_gen); // resize image (using intervention image from laravel package) and upload it to "upload/team" folder
            $save_url = 'upload/team/'.$name_gen; // save image url in a variable
            //--------------------------------------------------------------

            // insert data into database:
            Team::insert([
                'image' => $save_url,
                'name' => $request->name,
                'position' => $request->position,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'instagram' => $request->instagram,
                'created_at' => Carbon::now(), 
            ]);

            $notification= array(
                'message' => 'Team Member Inserted Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);
    }

    public function EditTeam($id){
        $team = Team::findOrFail($id); //If the record is found, it returns the model instance; if not, it throws a ModelNotFoundException
        return view('backend.team.edit_team',compact('team'));
    }

    public function UpdateTeam(Request $request){
        $team_id = $request->id; //get team id from hidden input 

        // We need to check if the user has uploaded a new image or not
        if($request->file('image')) // if user has uploaded a new image
        { 
            $image = $request->file('image'); // take an image file from form
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension(); // generate unique name for image e.g. 123.jpg (id+extension)
            Image::make($image)->resize(550,670)->save('upload/team/'.$name_gen); // resize image (using intervention image from laravel package) and upload it to "upload/team" folder
            $save_url = 'upload/team/'.$name_gen; // save image url in a variable

            Team::findOrFail($team_id)->update([
                'image' => $save_url,
                'name' => $request->name,
                'position' => $request->position,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'instagram' => $request->instagram,
                'created_at' => Carbon::now(), 
            ]);
    
            $notification= array(
                'message' => 'Team Updated with Image Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.team')->with($notification);
        } 
        // if user has not uploaded a new image
        else  {
            Team::findOrFail($team_id)->update([ // remove the image request and update the team without image
                'name' => $request->name,
                'position' => $request->position,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'instagram' => $request->instagram,
                'created_at' => Carbon::now(), 
            ]);
    
            $notification= array(
                'message' => 'Team Updated without Image Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.team')->with($notification);
        } // end else

       
    }

    public function DeleteTeam($id){
        // this logic is to delete the image from the team folder
        $item = Team::findOrFail($id);
        $img = $item->image;
        unlink($img);
        //--------------------------------------------------------

        // Now delete  the all row from database
        Team::findOrFail($id)->delete();
        $notification= array(
            'message' => 'Team Member Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    // ================================ Book Area All Methods =====================================

    public function BookArea(){
        //we have only one row in database, this data is shown one time in our website, so admin can updated (its id=1 in database (nothing else))
        $book= BookArea::find(1); // get the only one row of data
        return view('backend.bookarea.book_area',compact('book'));
    
    }

    public function BookAreaUpdate(Request $request){
        $book_id = $request->id; //get book id from hidden input
        // now we need to handle the updating with/without image as we did in update team method

        if($request->file('image')){ // if user has uploaded a new image
            $image = $request->file('image'); // take an image file from form
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension(); // generate unique name for image e.g. 123.jpg (id+extension)
            Image::make($image)->resize(1000,1000)->save('upload/bookarea/'.$name_gen);  // resize image (using intervention image from laravel package) and upload it to "upload/team" folder
            $save_url = 'upload/bookarea/'.$name_gen; // save image url in a variable
    
            BookArea::findOrFail($book_id)->update([
    
                'short_title' => $request->short_title,
                'main_title' => $request->main_title,
                'short_desc' => $request->short_desc,
                'link_url' => $request->link_url,
                'image' => $save_url, 
            ]);
    
            $notification = array(
                'message' => 'Book Area Updated With Image Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->back()->with($notification);
                     // if user has not uploaded a new image

        } else { // remove the image request and update the team without image
            BookArea::findOrFail($book_id)->update([
    
                'short_title' => $request->short_title,
                'main_title' => $request->main_title,
                'short_desc' => $request->short_desc,
                'link_url' => $request->link_url, 
            ]);
    
            $notification = array(
                'message' => 'Book Area Updated Without Image Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->back()->with($notification);
        } // End Eles 
    } // End Method

}
