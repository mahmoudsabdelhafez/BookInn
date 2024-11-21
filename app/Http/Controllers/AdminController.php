<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function AdminDashboard(){
        return view('admin.index');
    } // End Method


    //logout method
    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout(); // by laravel config/auth.php

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login'); // redirect to home
    }

    //login method
    public function AdminLogin()
    {
        return view('admin.admin-login');
    }

    // Admin Profile
    public function AdminProfile(){
        // get user id, by Auth(by laravel config/auth.php), user() method (by laravel config/auth.php)
        $id = Auth::user()->id;
        // Eloquent ORM, which fetches a record from the users table where the primary key matches the given $id
        $profileData = User::find($id);
        // compact: creates an associative array equivalent to ['profileData' => $profileData] and passes it to the view.
        return view('admin.admin-profile-view', compact('profileData'));
    }


    // Always remember: when the method is POST, you will need to pass Request $request
    public function AdminProfileStore(Request $request){
        $id = Auth::user()->id;
        $data = User::find($id);
        $data-> name = $request->name; 
         // $data->name is the column name in the users table
        // $request->name is the name of the input field in the form
        $data-> email = $request->email; 
        $data-> phone = $request->phone; 
        $data-> address = $request->address; 

        // handle image upload (explained in details in notion file)
        if($request->file('photo')){
            $file = $request->file('photo');
            // unlink: its will replace the old image(in admin_images folder) with the new one
            @unlink(public_path('upload/admin_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data['photo'] = $filename;
        }
        $data->save();  

        //Toaster notification
        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );
//  redirect the user back to the previous page they were on with toaster notification.
        return redirect()->back()->with($notification);
       
    }

    public function AdminChangePassword(){
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin-change-password', compact('profileData'));
    }

    public function AdminPasswordUpdate(Request $request){

        // validate request rules, i think we can do that in request class
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if(!Hash::check($request->old_password, Auth::user()->password)){

            $notification= array(
                'message' => 'Old Password Does Not Match',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification= array(
            'message' => 'Password Changed Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
        
    }
}


