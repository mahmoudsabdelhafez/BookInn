<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\BookArea;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Room;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\RoomBookedDate;
use App\Models\Booking;
use App\Models\BookingRoomList;
use App\Models\RoomNumber;
use Illuminate\Support\Facades\Auth;
use Stripe;
use App\Models\User;
use App\Notifications\BookingComplete;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail; // for email
use App\Mail\BookConfirm; // for email
use Barryvdh\DomPDF\Facade\Pdf;





class BookingController extends Controller
{



    public function Checkout(){
        if (Session::has('book_date')) {
            $book_data = Session::get('book_date');
            $room = Room::find($book_data['room_id']);
            $toDate = Carbon::parse($book_data['check_in']);
            $fromDate = Carbon::parse($book_data['check_out']);
            $nights = $toDate->diffInDays($fromDate);
            return view('frontend.checkout.checkout',compact('book_data','room','nights'));
         }else{
             $notification = array(
                 'message' => 'Something want to wrong!',
                 'alert-type' => 'error'
             ); 
             return redirect('/')->with($notification); 
         } // end else
             }// End Method

    public function BookingStore(Request $request){
        $validateData = $request->validate([
            'check_in' => 'required',
            'check_out' => 'required',
            'person' => 'required',
            'number_of_rooms' => 'required',
        ]);

         // Check if check_in or check_out is empty
        //  if (trim($request->check_in) === '' || trim($request->check_out) === '') {
        //     $notification = array(
        //         'message' => 'Please Select Check In and Check Out Date!',
        //         'alert-type' => 'error'
        //     );
        //     return redirect()->back()->with($notification);
        // }
        
       
        if($request->check_in == $request->check_out){
            $notification = array(
                'message' => 'Check In and Check Out Date is same!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
        if($request->check_in > $request->check_out){
            $notification = array(
                'message' => 'Check Out Date must be greater than Check In Date!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
        if ($request->available_room < $request->number_of_rooms) {
           
            $notification = array(
                'message' => 'Available Room is not enough!',
                'alert-type' => 'error'
            ); 
            return redirect()->back()->with($notification); 
        }
        Session::forget('book_date');
        $data = array();
        $data['number_of_rooms'] = $request->number_of_rooms;
        $data['available_room'] = $request->available_room;
        $data['person'] = $request->person;
        $data['check_in'] = date('Y-m-d',strtotime($request->check_in));
        $data['check_out'] = date('Y-m-d',strtotime($request->check_out));
        $data['room_id'] = $request->room_id;
        Session::put('book_date',$data);
        return redirect()->route('checkout');
    }// End Method 


    


    public function CheckoutStore(Request $request){

        $user = User::where('role','admin')->get();


        $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|numeric|digits_between:10,15',
            'address' => 'required|string|max:500',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|numeric|digits_between:4,10',
            'payment_method' => 'required',
 
        ]);

           $book_data = Session::get('book_date'); 
           $toDate = Carbon::parse($book_data['check_in']);
           $fromDate = Carbon::parse($book_data['check_out']);
           $total_nights = $toDate->diffInDays($fromDate);
           
           $room = Room::find($book_data['room_id']);
           $subtotal = $room->price * $total_nights * $book_data['number_of_rooms'] ;
           $discount = ($room->discount/100)*$subtotal;
           $total_price = $subtotal-$discount;
           $code = rand(000000000,999999999);

        // handle stripe payment
           if ($request->payment_method == 'Stripe') {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $s_pay = Stripe\Charge::create ([
                "amount" => $total_price * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Payment For Booking. Booking No ".$code,
            ]);
            if ($s_pay['status'] == 'succeeded') {
                $payment_status = 1;
                $transaction_id = $s_pay->id;
            }else{
                $notification = array(
                    'message' => 'Sorry Payment Field',
                    'alert-type' => 'error'
                ); 
                return redirect('/')->with($notification);  
            }
         } else{
            $payment_status = 0;
            $transaction_id = '';
         } 



           $data = new Booking();
           $data->rooms_id = $room->id;
           $data->user_id = Auth::user()->id;
           $data->check_in = date('Y-m-d',strtotime($book_data['check_in']));
           $data->check_out = date('Y-m-d',strtotime($book_data['check_out']));
           $data->person = $book_data['person'];
           $data->number_of_rooms = $book_data['number_of_rooms'];
           $data->total_night = $total_nights;

           $data->actual_price = $room->price;
           $data->subtotal = $subtotal;
           $data->discount = $discount;
           $data->total_price = $total_price;
           $data->payment_method = $request->payment_method;
           $data->transaction_id = '';
           $data->payment_status = 0;

           $data->name = $request->name;
           $data->email = $request->email;
           $data->phone = $request->phone;
           $data->country = $request->country;
           $data->state = $request->state;
           $data->zip_code = $request->zip_code;
           $data->address = $request->address;

           $data->code = $code;
           $data->status = 0;
           $data->created_at = Carbon::now();
           $data->save();

           // adding booked dates
           $sdate = date('Y-m-d',strtotime($book_data['check_in']));
           $edate = date('Y-m-d',strtotime($book_data['check_out']));
           $eldate = Carbon::create($edate)->subDay();
           $d_period = CarbonPeriod::create($sdate,$eldate);
           foreach ($d_period as $period) {
               $booked_dates = new RoomBookedDate();
               $booked_dates->booking_id = $data->id;
               $booked_dates->room_id = $room->id;
               $booked_dates->book_date = date('Y-m-d', strtotime($period));
               $booked_dates->save();
           }
   
           Session::forget('book_date');
   
           $notification = array(
               'message' => 'Booking Added Successfully',
               'alert-type' => 'success'
           );
           
           // ===================== To Send Notification =====================
           Notification::send($user, new BookingComplete($request->name));
           // ===================== To Send Notification =====================

           return redirect('/')->with($notification);  







    }// End Method 


// ==================================================================
    // Backend Methods
    public function BookingList(){

        $allData = Booking::orderBy('id','desc')->get(); // start from latest booking
        return view('backend.booking.booking_list',compact('allData'));

    }// End Method 

    public function EditBooking($id){

        $editData = Booking::with('room')->find($id); // same as Booking::find($id); get all row of this booking and it has a relation with room so u can access it
        return view('backend.booking.edit_booking',compact('editData'));
    }//end method


    public function UpdateBookingStatus(Request $request, $id){
        $booking = Booking::find($id);
        $booking->payment_status = $request->payment_status;
        $booking->status = $request->status;
        $booking->subtotal = $booking->actual_price * $booking->number_of_rooms * $booking->total_night;
        $booking->total_price = $booking->subtotal - $booking->discount;
        $booking->save();


         /// Start Sent Email ============================================================

         $sendmail = Booking::find($id);
         $data = [
             'check_in' => $sendmail->check_in,
             'check_out' => $sendmail->check_out,
             'name' => $sendmail->name,
             'email' => $sendmail->email,
             'phone' => $sendmail->phone,
         ];
         Mail::to($sendmail->email)->send(new BookConfirm($data));
         /// End Sent Email ===========================================================


        $notification = array(
            'message' => 'Information Updated Successfully',
            'alert-type' => 'success'
        ); 
        return redirect()->back()->with($notification);  
     }   // End Method 




     public function UpdateBooking(Request $request, $id){

        if ($request->available_room < $request->number_of_rooms) {

            $notification = array(
                'message' => 'No enough room available',
                'alert-type' => 'error'
            ); 
            return redirect()->back()->with($notification);  
        }

        $data = Booking::find($id);
        $data->number_of_rooms = $request->number_of_rooms;
        $data->check_in = date('Y-m-d', strtotime($request->check_in));
        $data->check_out = date('Y-m-d', strtotime($request->check_out));
        $toDate = Carbon::parse($data->check_in);
        $fromDate = Carbon::parse($data->check_out);
        $total_night = $toDate->diffInDays($fromDate);
        $data->total_night = $total_night;
        $data->subtotal = $data->actual_price * $data->number_of_rooms * $data->total_night;
        $data->total_price = $data->subtotal - $data->discount;
        $data->save();

        RoomBookedDate::where('booking_id', $id)->delete(); // delete old dates

        $sdate = date('Y-m-d',strtotime($request->check_in ));
        $edate = date('Y-m-d',strtotime($request->check_out));
        $eldate = Carbon::create($edate)->subDay();
        $d_period = CarbonPeriod::create($sdate,$eldate);
        foreach ($d_period as $period) {
            $booked_dates = new RoomBookedDate();
            $booked_dates->booking_id = $data->id;
            $booked_dates->room_id = $data->rooms_id;
            $booked_dates->book_date = date('Y-m-d', strtotime($period));
            $booked_dates->save();
        }

        $notification = array(
            'message' => 'Booking Updated Successfully',
            'alert-type' => 'success'
        ); 
        return redirect()->back()->with($notification);   

     }  // End Method 


     public function DeleteBooking($id){

        $data = Booking::find($id);
        $data->delete();

        $notification = array(
            'message' => 'Booking Deleted Successfully',
            'alert-type' => 'success'
        ); 
        return redirect()->back()->with($notification);
         
     }
     

     public function AssignRoom($booking_id)
{
    // Fetch the booking details based on the provided booking ID
    $booking = Booking::find($booking_id);

    // Step 1: Retrieve all dates associated with the current booking
    // These dates indicate when the booking spans.
    $booking_date_array = RoomBookedDate::where('booking_id', $booking_id)
        ->pluck('book_date')
        ->toArray();

    // Step 2: Identify all other bookings that overlap with these dates
    // This filters bookings for the same room type (rooms_id) that have any conflicting dates.
    $check_date_booking_ids = RoomBookedDate::whereIn('book_date', $booking_date_array)
        ->where('room_id', $booking->rooms_id)
        ->distinct() // Ensures unique booking IDs
        ->pluck('booking_id')
        ->toArray();

    // Step 3: Get the list of bookings that are for the same room type and overlap in dates
    $booking_ids = Booking::whereIn('id', $check_date_booking_ids)
        ->pluck('id')
        ->toArray();

    // Step 4: Fetch the room numbers already assigned to these bookings
    // This helps exclude room numbers that are unavailable for the current booking.
    $assign_room_ids = BookingRoomList::whereIn('booking_id', $booking_ids)
        ->pluck('room_number_id')
        ->toArray();

    // Step 5: Retrieve all active room numbers for the current booking's room type
    // Exclude room numbers that are already assigned to overlapping bookings.
    $room_numbers = RoomNumber::where('rooms_id', $booking->rooms_id)
        ->whereNotIn('id', $assign_room_ids) // Exclude already assigned rooms
        ->where('status', 'Active') // Ensure only active rooms are retrieved
        ->get();

    // Return the 'assign_room' view with the booking and available room numbers data
    return view('backend.booking.assign_room', compact('booking', 'room_numbers'));
}// End Method



     public function AssignRoomStore($booking_id,$room_number_id){
        $booking = Booking::find($booking_id);
        $check_data = BookingRoomList::where('booking_id',$booking_id)->count();
    // Check if the number of assigned rooms is less than the required number of rooms
    // This ensures that we don't assign more rooms than needed.
        if ($check_data < $booking->number_of_rooms) {
            $assign_data = new BookingRoomList();
            $assign_data->booking_id = $booking_id;
            $assign_data->room_id = $booking->rooms_id;
            $assign_data->room_number_id = $room_number_id;
            $assign_data->save();
            $notification = array(
             'message' => 'Room Assigned Successfully',
             'alert-type' => 'success'
         ); 
         return redirect()->back()->with($notification);   
         }else {
             $notification = array(
                 'message' => 'Room Already Assigned',
                 'alert-type' => 'error'
             ); 
             return redirect()->back()->with($notification);   
         }
 
      }// End Method


      public function AssignRoomDelete($id){
        $assign_room = BookingRoomList::find($id);
        $assign_room->delete();
        $notification = array(
            'message' => 'Assign Room Deleted Successfully',
            'alert-type' => 'success'
        ); 
        return redirect()->back()->with($notification); 
     }// End Method 


     public function MarkAsRead(Request $request , $notificationId){
        $user = Auth::user();
        $notification = $user->notifications()->where('id',$notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
        }
  return response()->json(['count' => $user->unreadNotifications()->count()]);
     }// End Method 



     public function UserBooking(){
        $id = Auth::user()->id;
        $allData = Booking::where('user_id',$id)->orderBy('id','desc')->get();
        return view('frontend.dashboard.user_booking',compact('allData'));
     }// End Method 


     public function UserInvoice($id){
        $editData = Booking::with('room')->find($id);
        $pdf = Pdf::loadView('backend.booking.booking_invoice',compact('editData'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
        return $pdf->download('invoice.pdf');
     }// End Method 




}
