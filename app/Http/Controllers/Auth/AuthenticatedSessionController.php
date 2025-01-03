<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Attempt to authenticate the user
            $request->authenticate();
    
            // Regenerate session on successful login
            $request->session()->regenerate();
    
            //=================== Start Logged In Notification with user name ===================
            $id = Auth::user()->id;
            $profileData = User::find($id);
            $username = $profileData->name;
            $notification = array(
                'message' => 'User '.$username.' Login Successfully',
                'alert-type' => 'success'
            );
            //=================== End Logged In Notification with user name =====================
    
            // Redirect based on user role
            $url = '';
            if ($request->user()->role === 'admin') {
                $url = '/admin/dashboard';
            } elseif ($request->user()->role === 'user') {
                $url = '/dashboard';
            }
            return redirect()->intended($url)->with($notification);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If authentication fails, show an error notification
            $notification = array(
                'message' => 'Invalid login credentials. Please try again.',
                'alert-type' => 'error'
            );
    
            return redirect()->back()->with($notification);
        }
    }
    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
