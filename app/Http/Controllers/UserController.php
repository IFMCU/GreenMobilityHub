<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\Coupon;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserCourse;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = User::where('guid', auth('api')->user()->guid)
            ->first();

        return ResponseController::getResponse($user, 200, 'Get Profile User Success');
    }

    public function insertData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone_number' => 'nullable|string',
            'email' => 'required|string',
            'role' => 'required|string',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
        $data = User::create([
            'phone_number' => $request['phone_number'],
            'name' => $request['name'],
            'email' => $request['email'],
            'role' => $request['role'],
            'password' => Hash::make('asd123'),
        ]);

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function showData()
    {
        $data = User::all();
        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }
        $dataTable = DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        return $dataTable;
    }
    public function getData($guid)
    {
        /// GET DATA
        $data = User::where('guid', '=', $guid)
            ->with('user_offer')
            ->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function reedem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_guid' => 'required|string|max:36',
            'offer_guid' => 'required|string|max:36',
            'point' => 'required|integer'

        ], MessagesController::messages());
        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
        /// GET DATA
        $user = User::where('guid', '=', $request['user_guid'])
            ->first();

        $offer = Offer::where('guid', '=', $request['offer_guid'])
            ->first();
        

        if($user->point < $request['point']){
            return ResponseController::getResponse(null, 400, "Point not Enough");
        }
        else{
            $user->point=$user->point-$request['point'];
            $offer->stock=$offer->stock-1;
            $user->save();
            $offer->save();
            Coupon::create([
                'user_guid' => $request->user_guid,
                'offer_guid' => $request->offer_guid,
                'code'=> random_int(100000, 999999),
            ]);
        }

        return ResponseController::getResponse(null, 200, 'Success');
    }

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:40',
            'phone_number' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|string',
            'google_id' => 'required|string',
            'role' => 'required|string',

        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $data = User::where('guid', '=', $request['guid'])->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }
        /// UPDATE DATA
        $data->guid = $request['guid'];
        $data->name = $request['name'];
        $data->role = $request['role'];
        $data->phone_number = $request['phone_number'];
        if (isset($request['email'])) {
            $data->email = $request['email'];
        }
        if (isset($request['google_id'])) {
            $data->google_id = $request['google_id'];
        }
        $data->save();

        return ResponseController::getResponse($data, 200, 'Success');
    }
    public function deleteData($guid)
    {
        $data = User::where('guid', '=', $guid)->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $data->delete();

        return ResponseController::getResponse(null, 200, 'Success');
    }
}
