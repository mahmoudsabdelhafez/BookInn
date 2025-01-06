<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Room;
use App\Models\Team;
use App\Models\BookArea;
use App\Models\Testimonial;
use Illuminate\Cache\RateLimiting\Limit;

class UserController extends Controller
{
    public function Index(){
    $room = Room::latest()->limit(4)->get();
    $team = Team::latest()->get();
    $testimonial = Testimonial::latest()->Limit(3)->get();
    $blog = BlogPost::latest()->limit(3)->get();


    $bookarea = BookArea::find(1);

        return view('frontend.index' , compact('room','team','bookarea','testimonial','blog'));
    }// End Method 


    public function UserProfile(){
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('frontend.dashboard.edit_profile',compact('profileData'));
    }// End Method 

    public function UserStore(Request $request){

        $id = Auth::user()->id;

        $request->validate([
            'name' => ['required', 'string', 'max:255'], // Required, must be a string, max 255 characters
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id], // Required, must be a valid email, unique except for the current user
            'phone' => ['required', 'digits_between:10,15'], // Required, must be exactly 10 digits
            'address' => ['required', 'string', 'max:255'], // Required, must be a string, max 255 characters
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Optional, must be an image, specific formats, max size 2MB
        ]);


        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        if($request->file('photo')){
            $file = $request->file('photo');
            @unlink(public_path('upload/user_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();  
            $file->move(public_path('upload/user_images'),$filename);
            $data['photo'] = $filename;
        }
        $data->save();
        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }// End Method


    public function UserLogout(Request $request){ // i copy this implementation from AuthenticatedSessionController (Destroy Method)
        $id = Auth::user()->id;
        $data = User::find($id);
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $notification = array(
            'message' => 'User ' . $data->name . ' Logout Successfully',
            'alert-type' => 'success'
        );
        return redirect('/login')->with($notification);
    }// End Method

    public function UserChangePassword(){

        return view('frontend.dashboard.user_change_password');
    }// End Method
    public function ChangePasswordStore(Request $request){
        // Validation 
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8'
        ]);
        if(!Hash::check($request->old_password, auth::user()->password)){
            $notification = array(
                'message' => 'Old Password is Incorrect',
                'alert-type' => 'error'
            );
    
            return back()->with($notification);
        }
        /// Update The New Password 
        User::whereId(auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        $notification = array(
            'message' => 'Password Changed Successfully',
            'alert-type' => 'success'
        );
        return back()->with($notification); 
    }// End Method 

}
