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
    public function EditRoom($id)
    {
        $editData = Room::find($id); // to get the room data
        $basic_facility = Facility::where('rooms_id', $id)->get(); // to get the facilities data
        $multiimgs = MultiImage::where('rooms_id', $id)->get(); // to get the facilities data
        $allroomNo = RoomNumber::where('rooms_id', $id)->get(); // to get all room numbers

        return view('backend.allroom.rooms.edit_rooms', compact('editData', 'basic_facility', 'multiimgs', 'allroomNo')); // here we pass the all data
    } //End Method 


    // Update on database
    public function UpdateRoom(Request $request, $id)
    {

        $request->validate([
            'roomtype_id' => 'required|string', 
            'total_adult' => 'required|integer|min:1', 
            'total_child' => 'required|integer|min:0', 
            'room_capacity' => 'required|integer|min:1', 
            'price' => 'required|numeric|min:0',
            'size' => 'required|numeric|min:0', 
            'view' => 'nullable|string', 
            'bed_style' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100', 
            'short_desc' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'facility_name' => 'required|array|min:1', // Ensure facilities are provided as an array and at least one is selected
            'facility_name.*' => 'required|string', // Ensure each facility name is a string
            'multi_img' => 'nullable|array', // Ensure multiple images are provided as an array
            'multi_img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ensure each image is valid
        ]);


        $room  = Room::find($id);

        //-------------------------------
        $roomName = RoomType::find($room->roomtype_id);
        $roomName->name = $request->roomtype_id;
        //-------------------------------

        $room->roomtype_id = $room->roomtype_id; // we already store the room type id in RoomType controller, so just call it
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
        $room->status = 1;




        /// Update Single Image 
        if ($request->file('image')) {
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(550, 850)->save('upload/roomimg/' . $name_gen);
            $room['image'] = $name_gen;
        }
        $room->save();
        $roomName->save();


        //// Update for Facility Table 
        // Check if the facility_name array is empty, meaning no facilities were selected
        if ($request->facility_name[0] == NULL) {
            // Display an error message if no facilities were selected
            $notification = array(
                'message' => 'Sorry! Not Any Basic Facility Select', // Error message to show
                'alert-type' => 'error' // Alert type to display the error message
            );

            // Redirect back to the previous page with the error notification
            return redirect()->back()->with($notification);
        } else {
            // If facilities are selected, first delete any existing facilities for the room (to avoid duplicates)
            Facility::where('rooms_id', $id)->delete(); // Deletes existing facility records associated with the room ID

            // Count the number of facilities selected in the form
            $facilities = Count($request->facility_name);

            // Loop through each selected facility and save it into the database
            for ($i = 0; $i < $facilities; $i++) {
                // Create a new Facility instance to insert the selected facility into the database
                $fcount = new Facility();

                // Set the room ID to associate the facility with the correct room
                $fcount->rooms_id = $room->id;

                // Set the facility name from the selected facility in the form
                $fcount->facility_name = $request->facility_name[$i];

                // Save the facility record in the database
                $fcount->save();
            } // end for
        } // end else 



        //// Update Multi Image 

        // Check if the room has been successfully saved
        if ($room->save()) {
            // Retrieve the uploaded files for multi-image input from the request
            $files = $request->multi_img;

            // If there are any files uploaded
            if (!empty($files)) {
                // Get all the images associated with the current room using the room ID and convert them into an array
                $subimage = MultiImage::where('rooms_id', $id)->get()->toArray();

                // Delete any existing multi-images associated with the current room to avoid duplicates
                MultiImage::where('rooms_id', $id)->delete();
            }

            // If files are uploaded (not empty)
            if (!empty($files)) {
                // Loop through each uploaded file
                foreach ($files as $file) {
                    // Generate a unique image name using the current date and time to avoid name conflicts
                    $imgName = date('YmdHi') . $file->getClientOriginalName();

                    // Move the uploaded file to the specified directory on the server (upload/roomimg/multi_img/)
                    $file->move('upload/roomimg/multi_img/', $imgName);

                    // Assign the image name to the subimage array (though this is not used further in the loop)
                    $subimage['multi_img'] = $imgName;

                    // Create a new MultiImage instance to store the image data in the database
                    $subimage = new MultiImage();

                    // Set the room ID in the MultiImage record to associate the image with the correct room
                    $subimage->rooms_id = $room->id;

                    // Set the image file name in the MultiImage record
                    $subimage->multi_img = $imgName;

                    // Save the MultiImage record to the database
                    $subimage->save();
                }
            }
        } // end if

        $notification = array(
            'message' => 'Room Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method 



    public function MultiImageDelete($id)
    {

        $deletedata = MultiImage::where('id', $id)->first();
        if ($deletedata) {
            $imagePath = $deletedata->multi_img;
            // Check if the file exists before unlinking 
            if (file_exists($imagePath)) {
                unlink($imagePath);
                echo "Image Unlinked Successfully";
            } else {
                echo "Image does not exist";
            }
            //  Delete the record form database 
            MultiImage::where('id', $id)->delete();
        }
        $notification = array(
            'message' => 'Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method 

    public function StoreRoomNumber(Request $request, $id)
    {

        $validatedData = $request->validate([
            'room_no' => 'required|numeric|unique:room_numbers,room_no', // Ensure room_no is unique in the room_numbers table
            'status' => 'required|in:Active,Inactive', // Limit status to specific values
        ]);


        try {
            // Create new RoomNumber record
            $data = new RoomNumber();
            $data->rooms_id = $id;
            $data->room_type_id = $request->room_type_id;
            $data->room_no = $request->room_no;
            $data->status = $request->status;
            $data->save();
    
            // Success notification
            $notification = [
                'message' => 'Room Number Added Successfully',
                'alert-type' => 'success'
            ];
            return redirect()->back()->with($notification);
        } catch (\Exception $e) {

            $notification = [
                'message' => 'Room Number Not Added Successfully',
                'alert-type' => 'error'
            ];
            // Catch any exceptions (optional)
            return redirect()->back()->with($notification);
        }


    } //End Method 

    public function EditRoomNumber($id)
    { //show edit room number page

        $editroomno = RoomNumber::find($id);
        return view('backend.allroom.rooms.edit_room_no', compact('editroomno'));
    } //End Method 
    public function UpdateRoomNumber(Request $request, $id)
    { // edit room number

        $validatedData = $request->validate([
            'room_no' => 'required|numeric|unique:room_numbers,room_no', // Ensure room_no is unique in the room_numbers table
            'status' => 'required|in:Active,Inactive', // Limit status to specific values
        ]);


        $data = RoomNumber::find($id);
        $data->room_no = $request->room_no;
        $data->status = $request->status;
        $data->save();
        $notification = array(
            'message' => 'Room Number Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('room.type.list')->with($notification);
    } //End Method 
    public function DeleteRoomNumber($id)
    { // delete room number
        RoomNumber::find($id)->delete();
        $notification = array(
            'message' => 'Room Number Deleted Successfully',
            'alert-type' => 'success'
        );

          return redirect()->back()->with($notification);

    } //End Method


    public function DeleteRoom(Request $request, $id)
    { // Delete All Room Data from all tables
        $room = Room::find($id);
        if (file_exists('upload/roomimg/' . $room->image) and ! empty($room->image)) {
            @unlink('upload/roomimg/' . $room->image);
        }
        $subimage = MultiImage::where('rooms_id', $room->id)->get()->toArray();
        if (!empty($subimage)) {
            foreach ($subimage as $value) {
                if (!empty($value)) {
                    @unlink('upload/roomimg/multi_img/' . $value['multi_img']);
                }
            }
        }
        RoomType::where('id', $room->roomtype_id)->delete();
        MultiImage::where('rooms_id', $room->id)->delete();
        Facility::where('rooms_id', $room->id)->delete();
        RoomNumber::where('rooms_id', $room->id)->delete();
        $room->delete();
        $notification = array(
            'message' => 'Room Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method



}
