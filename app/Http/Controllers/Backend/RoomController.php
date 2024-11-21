<?php

namespace App\Http\Controllers\Backend;

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomNumber;

use App\Models\Facility;
use Intervention\Image\Facades\Image;
use App\Models\MultiImage;

use Carbon\Carbon;

class RoomController extends Controller
{

    //show edit page for specific room
    public function EditRoom($id){
        $editData = Room::find($id); // to get the room data
        $basic_facility = Facility::where('rooms_id', $id)->get(); // to get the facilities data
        $multiimgs = MultiImage::where('rooms_id', $id)->get(); // to get the facilities data
        $allroomNo = RoomNumber::where('rooms_id',$id)->get(); // to get all room numbers

        return view('backend.allroom.rooms.edit_rooms',compact('editData', 'basic_facility', 'multiimgs', 'allroomNo')); // here we pass the all data
    } //End Method 


    // Update on database
    public function UpdateRoom(Request $request, $id){
        $room  = Room::find($id);  

        //-------------------------------
        $roomName = RoomType::find($room->roomtype_id);
        $roomName->name = $request->roomtype_id;
        //-------------------------------
        
        $room->roomtype_id = $room->roomtype_id;
        $room->total_adult = $request->total_adult;
        $room->total_child = $request->total_child;
        $room->room_capacity = $request->room_capacity;
        $room->price = $request->price;
        $room->size = $request->size;
        $room->view = $request->view;
        $room->bed_style = $request->bed_style;
        $room->discount = $request->discount;
        $room->short_desc = $request->short_desc;
        $room->description = $request->description; 



        /// Update Single Image 
        if($request->file('image')){
        $image = $request->file('image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(550,850)->save('upload/roomimg/'.$name_gen);
        $room['image'] = $name_gen; 
        }
        $room->save();
        $roomName->save();


    //// Update for Facility Table 
    if($request->facility_name[0] == NULL){
        $notification = array(
            'message' => 'Sorry! Not Any Basic Facility Select',
            'alert-type' => 'error'
        );

        return redirect()->back()->with($notification);
    } else{
        Facility::where('rooms_id',$id)->delete();
        $facilities = Count($request->facility_name);
        for($i=0; $i < $facilities; $i++ ){
            $fcount = new Facility();
            $fcount->rooms_id = $room->id;
            $fcount->facility_name = $request->facility_name[$i];
            $fcount->save();
        } // end for
    } // end else 


        //// Update Multi Image 
        if($room->save()){
            $files = $request->multi_img;
            if(!empty($files)){
                $subimage = MultiImage::where('rooms_id',$id)->get()->toArray();
                MultiImage::where('rooms_id',$id)->delete();
 
            }
            if(!empty($files)){
                foreach($files as $file){
                    $imgName = date('YmdHi').$file->getClientOriginalName();
                    $file->move('upload/roomimg/multi_img/',$imgName);
                    $subimage['multi_img'] = $imgName;
                    $subimage = new MultiImage();
                    $subimage->rooms_id = $room->id;
                    $subimage->multi_img = $imgName;
                    $subimage->save();
                }
            }
        } // end if
        $notification = array(
            'message' => 'Room Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification); 
    }//End Method 



    public function MultiImageDelete($id){

        $deletedata = MultiImage::where('id',$id)->first();
        if($deletedata){
            $imagePath = $deletedata->multi_img;
            // Check if the file exists before unlinking 
            if (file_exists($imagePath)) {
               unlink($imagePath);
               echo "Image Unlinked Successfully";
            }else{
                echo "Image does not exist";
            }
            //  Delete the record form database 
            MultiImage::where('id',$id)->delete();
        }
        $notification = array(
            'message' => 'Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification); 
    }//End Method 

    public function StoreRoomNumber(Request $request,$id){
        $data = new RoomNumber();
        $data->rooms_id = $id;
        $data->room_type_id = $request->room_type_id;
        $data->room_no = $request->room_no;
        $data->status = $request->status;
        $data->save();
        $notification = array(
            'message' => 'Room Number Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification); 
    }//End Method 

    public function EditRoomNumber($id){ //show edit room number page

        $editroomno = RoomNumber::find($id);
        return view('backend.allroom.rooms.edit_room_no',compact('editroomno'));

    }//End Method 
    public function UpdateRoomNumber(Request $request, $id){ // edit room number
        $data = RoomNumber::find($id);
        $data->room_no = $request->room_no;
        $data->status = $request->status;
        $data->save();
       $notification = array(
            'message' => 'Room Number Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('room.type.list')->with($notification); 
    }//End Method 
    public function DeleteRoomNumber($id){ // delete room number
        RoomNumber::find($id)->delete();
        $notification = array(
            'message' => 'Room Number Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('room.type.list')->with($notification); 

    }//End Method




}