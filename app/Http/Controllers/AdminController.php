<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class AdminController extends Controller
{
    public function AdminDashboard()
    {


        $bookings = Booking::latest()->get();
        $pending = Booking::where('status', '0')->get();
        $complete = Booking::where('status', '1')->get();
        $totalPrice = Booking::sum('total_price');

        $today = Carbon::now()->toDateString();
        $todayprice = Booking::whereDate('created_at', $today)->sum('total_price');
        $allData = Booking::orderBy('id', 'desc')->limit(10)->get();

        return view('admin.index', compact('bookings', 'pending', 'complete', 'totalPrice', 'todayprice', 'allData'));
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
    public function AdminProfile()
    {
        // get user id, by Auth(by laravel config/auth.php), user() method (by laravel config/auth.php)
        $id = Auth::user()->id;
        // Eloquent ORM, which fetches a record from the users table where the primary key matches the given $id
        $profileData = User::find($id);
        // compact: creates an associative array equivalent to ['profileData' => $profileData] and passes it to the view.
        return view('admin.admin-profile-view', compact('profileData'));
    }


    // Always remember: when the method is POST, you will need to pass Request $request
    public function AdminProfileStore(Request $request)
    {

        $id = Auth::user()->id;


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,  // email must be unique except for the logged-in user's email
            'phone' => 'nullable|numeric|digits_between:10,15',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // photo should be an image and less than 2MB
        ]);


        $data = User::find($id);
        $data->name = $request->name;
        // $data->name is the column name in the users table
        // $request->name is the name of the input field in the form
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        // handle image upload (explained in details in notion file)
        if ($request->file('photo')) {
            $file = $request->file('photo');
            // unlink: its will replace the old image(in admin_images folder) with the new one
            @unlink(public_path('upload/admin_images/' . $data->photo));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data['photo'] = $filename;
        }
        $data->save();

        //Toaster notification
        $notification = array( // toaster notification store on session 
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );
        //  redirect the user back to the previous page they were on with toaster notification.
        return redirect()->back()->with($notification);
    }

    public function AdminChangePassword()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin-change-password', compact('profileData'));
    }

    public function AdminPasswordUpdate(Request $request)
    {

        // validate request rules, i think we can do that in request class
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->old_password, Auth::user()->password)) {

            $notification = array(
                'message' => 'Old Password Does Not Match',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password Changed Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }



    public function AllAdmin()
    {
        $alladmin = User::where('role', 'admin')->get();
        return view('backend.pages.admin.all_admin', compact('alladmin'));
    } // End Method 

    public function AddAdmin()
    {
        $roles = Role::all();
        return view('backend.pages.admin.add_admin', compact('roles'));
    } // End Method 


    public function StoreAdmin(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|numeric|digits_between:10,15',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:8',
            'roles' => 'required',

        ]);
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        $user->role = 'admin';
        $user->status = 'active';

        $user->save();

        if ($request->roles) {
            // Retrieve role name from the role ID
            $role = Role::find($request->roles);
            if ($role) {
                $user->assignRole($role->name); // Assign the role by name
            } else {
                return redirect()->route('all.admin')->withErrors([
                    'error' => 'Invalid Role ID provided.',
                ]);
            }
        }

        $notification = array(
            'message' => 'Admin User Created Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.admin')->with($notification);
    } // End Method


    public function EditAdmin($id)
    {
        $user = User::find($id);
        $roles = Role::all();
        return view('backend.pages.admin.edit_admin', compact('user', 'roles'));
    } // End Method 
    public function UpdateAdmin(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|numeric|digits_between:10,15',
            'address' => 'nullable|string|max:500',
            'roles' => 'required',

        ]);


        $user = User::find($id);

        if (!$user) {
            return redirect()->route('all.admin')->withErrors([
                'error' => 'Admin User not found.',
            ]);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->role = 'admin';
        $user->status = 'active';

        $user->save();

        // Detach all roles from the user
        $user->roles()->detach();

        if ($request->roles) {
            // Retrieve role name from the role ID
            $role = Role::find($request->roles);
            if ($role) {
                $user->assignRole($role->name); // Assign the role by name
            } else {
                return redirect()->route('all.admin')->withErrors([
                    'error' => 'Invalid Role ID provided.',
                ]);
            }
        }

        $notification = array(
            'message' => 'Admin User Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.admin')->with($notification);
    } // End Method

    public function DeleteAdmin($id)
    {
        $user = User::find($id);
        if (!is_null($user)) {
            $user->delete();
        }
        $notification = array(
            'message' => 'Admin User Delete Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method



}
