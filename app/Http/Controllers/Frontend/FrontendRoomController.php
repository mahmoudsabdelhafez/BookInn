<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookArea;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Room;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\RoomBookedDate;
use App\Models\Booking;
class FrontendRoomController extends Controller
{
    public function AllFrontendRoomList(){
        $rooms = Room::latest()->get();
        return view('frontend.room.all_rooms',compact('rooms'));
    } // End Method
    
    
    public function RoomDetailsPage($id){
        $roomdetails = Room::find($id);
        $multiImage = MultiImage::where('rooms_id',$id)->get();
        $facility = Facility::where('rooms_id',$id)->get();
        $otherRooms = Room::where('id','!=', $id)->orderBy('id','DESC')->limit(2)->get();
        return view('frontend.room.room_details',compact('roomdetails','multiImage','facility','otherRooms'));   
     } // End Method 

     public function BookingSearch(Request $request){
   
        // Flash the input data to preserve it across redirects (e.g., for old values in form fields)
        $request->flash();
    
        // Check if the check-in and check-out dates are the same
        if ($request->check_in == $request->check_out) {
    
            // Create a notification to show an error message if the dates are the same
            $notification = array(
                'message' => 'Something went wrong',  // Error message
                'alert-type' => 'error'  // Alert type for showing the error message
            );
        
            // Redirect back with the notification
            return redirect()->back()->with($notification);
        }
    
        // Convert the check-in and check-out dates into 'Y-m-d' format
        $sdate = date('Y-m-d', strtotime($request->check_in)); // Start date
        $edate = date('Y-m-d', strtotime($request->check_out)); // End date
    
        // Subtract one day from the end date to ensure the booking period excludes the last day
        $alldate = Carbon::create($edate)->subDay(); 
    
        // Create a date period object to generate all the dates between the start and end dates
        $d_period = CarbonPeriod::create($sdate, $alldate);
        
        // Initialize an array to store the date range
        $dt_array = [];
    
        // Loop through each date in the period and store it in the $dt_array
        foreach ($d_period as $period) {
            array_push($dt_array, date('Y-m-d', strtotime($period)));  // Add each date to the array
        }
    
        // Query the RoomBookedDate model to find booking IDs for the dates in $dt_array
        // The distinct() method ensures no duplicate booking IDs are retrieved
        $check_date_booking_ids = RoomBookedDate::whereIn('book_date', $dt_array)
                                                 ->distinct()  // Ensure unique booking IDs
                                                 ->pluck('booking_id')  // Get only the 'booking_id' column
                                                 ->toArray();  // Convert the result into an array
    
        // Retrieve all rooms that are active (status = 1) along with a count of their room numbers
        $rooms = Room::withCount('room_numbers')  // Get the count of associated room numbers
                     ->where('status', 1)  // Only select rooms that are active
                     ->get();  // Fetch all matching rooms
    
        // Return the search result view with the rooms and check_date_booking_ids data
        return view('frontend.room.search_room', compact('rooms', 'check_date_booking_ids'));
    
    } // End Method
    
    public function SearchRoomDetails(Request $request,$id){
        $request->flash();
        $roomdetails = Room::find($id);
        $multiImage = MultiImage::where('rooms_id',$id)->get();
        $facility = Facility::where('rooms_id',$id)->get();
        $otherRooms = Room::where('id','!=', $id)->orderBy('id','DESC')->limit(2)->get();
        $room_id = $id;
        return view('frontend.room.search_room_details',compact('roomdetails','multiImage','facility','otherRooms','room_id'));
    }// End Method 



    public function CheckRoomAvailability(Request $request){
        $sdate = date('Y-m-d',strtotime($request->check_in));
        $edate = date('Y-m-d',strtotime($request->check_out));
        $alldate = Carbon::create($edate)->subDay();
        $d_period = CarbonPeriod::create($sdate,$alldate);
        $dt_array = [];
        foreach ($d_period as $period) {
           array_push($dt_array, date('Y-m-d', strtotime($period)));
        }
        $check_date_booking_ids = RoomBookedDate::whereIn('book_date',$dt_array)->distinct()->pluck('booking_id')->toArray();
        $room = Room::withCount('room_numbers')->find($request->room_id);
        $bookings = Booking::withCount('assign_rooms')->whereIn('id',$check_date_booking_ids)->where('rooms_id',$room->id)->get()->toArray();
        $total_book_room = array_sum(array_column($bookings,'assign_rooms_count'));
        $av_room = @$room->room_numbers_count-$total_book_room;
        $toDate = Carbon::parse($request->check_in);
        $fromDate = Carbon::parse($request->check_out);
        $nights = $toDate->diffInDays($fromDate);
        return response()->json(['available_room'=>$av_room, 'total_nights'=>$nights ]);
    }// End Method 



}





