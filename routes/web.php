<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\RoomTypeController;
use App\Http\Controllers\Backend\TeamController;
use App\Http\Controllers\Backend\RoomController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Backend\RoomListController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Frontend\FrontendRoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Booking;
use App\Models\Team;
use App\Http\Controllers\Backend\CommentController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Load The main page of the website
Route::get('/',[UserController::class, 'index']);


// User Dashboard, user must be logged in
Route::get('/dashboard', function () {
    return view('frontend.dashboard.user_dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'UserProfile'])->name('user.profile');
    Route::post('/profile/store', [UserController::class, 'UserStore'])->name('profile.store');
    Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
    Route::get('/user/change/password', [UserController::class, 'UserChangePassword'])->name('user.change.password');
    Route::post('/password/change/password', [UserController::class, 'ChangePasswordStore'])->name('password.change.store');


});

require __DIR__.'/auth.php'; // to include the auth routes routes/auth.php (built in by breeze)






//====================================================================================================

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






// Room Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {

    Route::controller(TeamController::class)->group(function () {
        Route::get('/all/team', 'AllTeam')->name('all.team');
        Route::get('/add/team', 'AddTeam')->name('add.team');
        Route::post('/team/store', 'StoreTeam')->name('team.store');
        Route::get('/edit/store/{id}', 'EditTeam')->name('edit.team'); // here we should pass team id (show edit page)
        Route::post('/team/update', 'UpdateTeam')->name('team.update'); // update team on db
        Route::get('/delete/team/{id}', 'DeleteTeam')->name('delete.team');
        Route::get('/book/area', 'BookArea')->name('book.area');
        Route::post('/book/area/update', 'BookAreaUpdate')->name('book.area.update');
        
    });




    Route::controller(RoomTypeController::class)->group(function () {
        Route::get('/room/type/list', 'RoomTypeList')->name('room.type.list');
        Route::get('/add/room/type', 'AddRoomType')->name('add.room.type');
        Route::post('/room/type/store', 'RoomTypeStore')->name('room.type.store');
    });

    Route::controller(RoomController::class)->group(function () {
        Route::get('/edit/room/{id}', 'EditRoom')->name('edit.room'); // here we should pass room id (show edit page)
        Route::post('/update/room/{id}', 'UpdateRoom')->name('update.room'); // here we should pass room id (Update the data)
        Route::get('/multi/image/delete/{id}', 'MultiImageDelete')->name('multi.image.delete'); // here we delete multi image and redirect to the same view
        
        Route::post('/store/room/no/{id}', 'StoreRoomNumber')->name('store.room.no'); // add room number 
        Route::get('/edit/roomno/{id}', 'EditRoomNumber')->name('edit.roomno'); // show edit room number page
        Route::post('/update/roomno/{id}', 'UpdateRoomNumber')->name('update.roomno'); // update room number
        Route::get('/delete/roomno/{id}', 'DeleteRoomNumber')->name('delete.roomno'); // delete room number

        Route::get('/delete/room/{id}', 'DeleteRoom')->name('delete.room'); // delete room from all tables


  
    });
    

    Route::controller(BookingController::class)->group(function () {
        Route::get('/booking/list', 'BookingList')->name('booking.list'); // show all booking
        Route::get('/edit/booking/{id}', 'EditBooking')->name('edit.booking'); //edit specific booking

        // booking Update 
    Route::post('/update/booking/status/{id}', 'UpdateBookingStatus')->name('update.booking.status');

    Route::post('/update/booking/{id}', 'UpdateBooking')->name('update.booking');

    Route::get('/assign_room/{id}', 'AssignRoom')->name('assign_room');

    Route::get('/assign_room/store/{booking_id}/{room_number_id}', 'AssignRoomStore')->name('assign_room_store');

    Route::get('/assign_room_delete/{id}', 'AssignRoomDelete')->name('assign_room_delete');
  
    });



    Route::controller(RoomListController::class)->group(function () {
        Route::get('/view/room/list', 'ViewRoomList')->name('view.room.list'); // view room list
        Route::get('/add/room/list', 'AddRoomList')->name('add.room.list'); // show add booking page
        Route::post('/store/roomlist', 'StoreRoomList')->name('store.roomlist'); // store new booking in db
        Route::get('/download/invoice/{id}', 'DownloadInvoice')->name('download.invoice'); // before that we install and publish the required package (dompdf)


  
    });

   


    Route::controller(SettingController::class)->group(function () {
        Route::get('/smtp/setting', 'SmtpSetting')->name('smtp.setting'); // show smtp setting page
        Route::post('/smtp/update', 'SmtpUpdate')->name('smtp.update'); // update smtp setting

         /// Testimonial All Route 
 Route::controller(TestimonialController::class)->group(function(){
    Route::get('/all/testimonial', 'AllTestimonial')->name('all.testimonial'); // show all testimonial
    Route::get('/add/testimonial', 'AddTestimonial')->name('add.testimonial');// show add testimonial page page
    Route::post('/store/testimonial', 'StoreTestimonial')->name('testimonial.store'); // store testimonial on  db

    Route::get('/edit/testimonial/{id}', 'EditTestimonial')->name('edit.testimonial'); // show edit testimonial page
    Route::post('/update/testimonial', 'UpdateTestimonial')->name('testimonial.update'); // update testimonial on db
    Route::get('/delete/testimonial/{id}', 'DeleteTestimonial')->name('delete.testimonial'); // delete testimonial

     /// Blog Category All Route 
 Route::controller(BlogController::class)->group(function(){
    Route::get('/blog/category', 'BlogCategory')->name('blog.category');
    Route::post('/store/blog/category', 'StoreBlogCategory')->name('store.blog.category');
    Route::get('/edit/blog/category/{id}', 'EditBlogCategory'); // show edit blog category
    Route::post('/update/blog/category', 'UpdateBlogCategory')->name('update.blog.category'); // update blog category
    Route::get('/delete/blog/category/{id}', 'DeleteBlogCategory')->name('delete.blog.category');

 });


 Route::controller(BlogController::class)->group(function(){
    Route::get('/all/blog/post', 'AllBlogPost')->name('all.blog.post');
    Route::get('/add/blog/post', 'AddBlogPost')->name('add.blog.post');
    Route::post('/store/blog/post', 'StoreBlogPost')->name('store.blog.post');
    Route::get('/edit/blog/post/{id}', 'EditBlogPost')->name('edit.blog.post');
    Route::post('/update/blog/post', 'UpdateBlogPost')->name('update.blog.post');
    Route::get('/delete/blog/post/{id}', 'DeleteBlogPost')->name('delete.blog.post');

 });


      
});

       

  
    });
}); // End Room Group Middleware


Route::controller(FrontendRoomController::class)->group(function () {
    Route::get('/rooms/', 'AllFrontendRoomList')->name('froom.all');
    Route::get('/room/details/{id}', 'RoomDetailsPage');
    Route::get('/bookings/','BookingSearch')->name('booking.search');
    Route::get('/search/room/details/{id}', 'SearchRoomDetails')->name('search_room_details');
    Route::get('/check_room_availability/', 'CheckRoomAvailability')->name('check_room_availability');



});


// Auth Middleware User must have login for access this route 
Route::middleware(['auth'])->group(function(){
    /// CHECKOUT ALL Route 
Route::controller(BookingController::class)->group(function(){
   Route::get('/checkout/', 'Checkout')->name('checkout');
   Route::post('/booking/store/', 'BookingStore')->name('user_booking_store'); // store booking data on session
   Route::post('/checkout/store/', 'CheckoutStore')->name('checkout.store'); // store checkout data on booking and booked_dates tables
   Route::match(['get', 'post'],'/stripe_pay', [BookingController::class, 'stripe_pay'])->name('stripe_pay');



    

});
}); // End Group Auth Middleware


 /// Frontend Blog  All Route 
 Route::controller(BlogController::class)->group(function(){
    Route::get('/blog/details/{slug}', 'BlogDetails');
    Route::get('/blog/cat/list/{id}', 'BlogCatList');
    Route::get('/blog', 'BlogList')->name('blog.list');

});

Route::controller(CommentController::class)->group(function(){
 
    Route::post('/store/comment/', 'StoreComment')->name('store.comment');
   
 
});