<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\RoomType;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class RoomTypeController extends Controller
{
    

    public function RoomTypeList(){

        $allData = RoomType::orderBy('id','desc')->get();
        return view('backend.allroom.roomtype.view_roomtype',compact('allData'));

    }

    public function AddRoomType(){
        return view('backend.allroom.roomtype.add_roomtype');
    }

    public function RoomTypeStore(Request $request){

        
        $roomtype_id= RoomType::insertGetId([ // insert a new record into a database table and immediately retrieve the ID of the newly inserted row
            'name' => $request->name,
            'created_at' => Carbon::now(),
        ]);

        Room::insert([
            'roomtype_id' => $roomtype_id, // first variable is the room type id in Room db and second one is the value that we get in the above line when we insert to RoomType db
            // now when we insert to RoomType db we will insert the roomtype_id in Room db
        ]);

        $notification= array(
            'message' => 'Room Type Inserted Successfully',
            'alert-type' => 'success'
        );  

        return redirect()->route('room.type.list')->with($notification);
    }


}