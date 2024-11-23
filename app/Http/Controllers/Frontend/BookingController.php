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
use Illuminate\Support\Facades\Mail; // for email
use App\Mail\BookConfirm; // for email


class BookingController extends Controller
{


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
        $booking->save();

        //Start Sending Mail
        // $sendmail = Booking::find($id);
        // $data = [
        //     'check_in' => $sendmail->check_in,
        //     'check_out' => $sendmail->check_out,
        //     'name' => $sendmail->name,
        //     'email' => $sendmail->email,
        //     'phone' => $sendmail->phone,
        // ];
       
        

        // Mail::to($sendmail->email)->send(new BookConfirm($data));
        //End Sending Mail
        $notification = array(
            'message' => 'Information Updated Successfully',
            'alert-type' => 'success'
        ); 
        return redirect()->back()->with($notification);  
     }   // End Method 




     public function UpdateBooking(Request $request, $id){

        // here we check if there is enough available rooms 
        if ($request->available_room < $request->number_of_rooms) {
            $notification = array(
                'message' => 'Something Want To Wrong!',
                'alert-type' => 'error'
            ); 
            return redirect()->back()->with($notification);  
        } // end if


        // here we update the booking data 
        $data = Booking::find($id);
        $data->number_of_rooms = $request->number_of_rooms;
        $data->check_in = date('Y-m-d', strtotime($request->check_in));
        $data->check_out = date('Y-m-d', strtotime($request->check_out));
        $data->save();
        //-------------------------------

        // here we delete the old booked dates 
        BookingRoomList::where('booking_id', $id)->delete();
        RoomBookedDate::where('booking_id', $id)->delete(); // i don't no what we need it for?
        //-------------------------------

        // here we add new booked dates (we add the all days that this room was booked by this booking)
        $sdate = date('Y-m-d',strtotime($request->check_in ));
        $edate = date('Y-m-d',strtotime($request->check_out));
        $eldate = Carbon::create($edate)->subDay();
        $d_period = CarbonPeriod::create($sdate,$eldate);
        foreach ($d_period as $period) { // loop over all days in the booking period
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
     

     public function AssignRoom($booking_id){
        $booking = Booking::find($booking_id);
        $booking_date_array = RoomBookedDate::where('booking_id',$booking_id)->pluck('book_date')->toArray(); // return array od booking dates
        $check_date_booking_ids = RoomBookedDate::whereIn('book_date',$booking_date_array)->where('room_id',$booking->rooms_id)->distinct()->pluck('booking_id')->toArray(); // return the dates that this room was booked
        
        $booking_ids = Booking::whereIn('id',$check_date_booking_ids)->pluck('id')->toArray(); // get the booking ids for this room
        $assign_room_ids = BookingRoomList::whereIn('booking_id',$booking_ids)->pluck('room_number_id')->toArray();
        $room_numbers = RoomNumber::where('rooms_id',$booking->rooms_id)->whereNotIn('id',$assign_room_ids)->where('status','Active')->get(); // get all room numbers that this room has
        return view('backend.booking.assign_room',compact('booking','room_numbers'));
        

     } // End Method 


     public function AssignRoomStore($booking_id,$room_number_id){
        $booking = Booking::find($booking_id);
        $check_data = BookingRoomList::where('booking_id',$booking_id)->count();
        if ($check_data < $booking->number_of_rooms) {
            $assign_data = new BookingRoomList();
            $assign_data->booking_id = $booking_id;
            $assign_data->room_id = $booking->rooms_id;
            $assign_data->room_number_id = $room_number_id;
            $assign_data->save();
            $notification = array(
             'message' => 'Room Assign Successfully',
             'alert-type' => 'success'
         ); 
         return redirect()->back()->with($notification);   
         }else {
             $notification = array(
                 'message' => 'Room Already Assign',
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



}
