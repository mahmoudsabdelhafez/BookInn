<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\RoomTypeController;
use App\Http\Controllers\Backend\TeamController;
use App\Http\Controllers\Backend\RoomController;
use App\Models\Team;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Admin Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/logout' , [AdminController::class, 'AdminLogout'])->name('admin.logout'); 
    // admin profile page
    Route::get('/admin/profile' , [AdminController::class, 'AdminProfile'])->name('admin.profile'); 
    // update profile on db
    Route::post('/admin/profile/store' , [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store'); 

    // Admin Change Password page
    Route::get('/admin/change/password' , [AdminController::class, 'AdminChangePassword'])->name('admin.change.password'); 
    // update password on db
    Route::post('/admin/password/update' , [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update'); 
}); // End Admin Group Middleware


Route::get('/admin/login' , [AdminController::class, 'AdminLogin'])->name('admin.login'); 



// Team Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::controller(TeamController::class)->group(function () {
        Route::get('/all/team', 'AllTeam')->name('all.team');
        Route::get('/add/team', 'AddTeam')->name('add.team');
        Route::post('/team/store', 'StoreTeam')->name('team.store');
        Route::get('/edit/store/{id}', 'EditTeam')->name('edit.team'); // here we should pass team id (show edit page)
        Route::post('/team/update', 'UpdateTeam')->name('team.update'); // update team on db
        Route::get('/delete/team/{id}', 'DeleteTeam')->name('delete.team');
        
    });
}); // End Team Group Middleware


// Book Area Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::controller(TeamController::class)->group(function () {
        Route::get('/book/area', 'BookArea')->name('book.area');
        Route::post('/book/area/update', 'BookAreaUpdate')->name('book.area.update');
    });
}); // End Book Area Group Middleware


// RoomType Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::controller(RoomTypeController::class)->group(function () {
        Route::get('/room/type/list', 'RoomTypeList')->name('room.type.list');
        Route::get('/add/room/type', 'AddRoomType')->name('add.room.type');
        Route::post('/room/type/store', 'RoomTypeStore')->name('room.type.store');
    });
}); // End RoomType Group Middleware


// Room Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::controller(RoomController::class)->group(function () {
        Route::get('/edit/room/{id}', 'EditRoom')->name('edit.room'); // here we should pass room id (show edit page)
        Route::post('/update/room/{id}', 'UpdateRoom')->name('update.room'); // here we should pass room id (Update the data)
        Route::get('/multi/image/delete/{id}', 'MultiImageDelete')->name('multi.image.delete'); // here we delete multi image and redirect to the same view
        
        Route::post('/store/room/no/{id}', 'StoreRoomNumber')->name('store.room.no'); // add room number 
        Route::get('/edit/roomno/{id}', 'EditRoomNumber')->name('edit.roomno'); // show edit room number page
        Route::post('/update/roomno/{id}', 'UpdateRoomNumber')->name('update.roomno'); // update room number
        Route::get('/delete/roomno/{id}', 'DeleteRoomNumber')->name('delete.roomno'); // delete room number

  
    });
}); // End Room Group Middleware
