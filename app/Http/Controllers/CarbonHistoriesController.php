<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MessagesController;
use App\Models\CarbonHistory;
use App\Models\PointHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class CarbonHistoriesController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Index method called with request:', $request->all());
    
            // Fetch the data from the database
            $data = CarbonHistory::orderBy('created_at', 'desc')
                ->get();
    
            // Log the fetched data
            Log::info('Fetched data:', $data->toArray());
    
            // Return the response
            return ResponseController::getResponse($data, 200, 'Success');
        } catch (\Exception $e) {
            // Log the exception message and stack trace
            Log::error('Error in index method:', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
    
            // Return an error response
            return ResponseController::getResponse(null, 500, 'Internal Server Error');
        }
    }

    public function getData($guid)
    {
        /// GET DATA
        $data = CarbonHistory::where('guid', '=', $guid)
            ->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function getAllDataTable()
    {
        $data = CarbonHistory::get();

        $dataTable = DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        return $dataTable;
    }

    public function insertData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'old_km' => 'required|numeric',
            'new_km' => 'required|numeric',
            'files_url.*' => 'nullable|file|mimes:jpg,jpeg,png|max:8192',
            'status'=>'required|string',
            'user_guid' => 'required|uuid|exists:users,guid',
        ], MessagesController::messages());
    
        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $emissionRates = [
            'mobil' => 0.21,
            'motor' => 0.1,
            'bus' => 0.27
        ];

        $hasil =0;

        $kmdiff = ($request->new_km - $request->old_km);

        if(array_key_exists($request->type, $emissionRates)) {
            $hasil = $emissionRates[$request->type] * $kmdiff;
        } else {
            return 0;
        }
    
        $data = CarbonHistory::create([
            'type' => $request->type,
            'old_km' => $request->old_km,
            'new_km' => $request->new_km,
            'km_diff' => $kmdiff ,
            'carbon_total' => $hasil,
            'status' => $request->status,
            'files_url' => $request->files_url,
            'user_guid' => $request->user_guid,
        ]);

        if($hasil <=49 && $request->status == 'approved'){
            PointHistory::create([
                'total' => $kmdiff,
                'point' => floor(49-$hasil),
                'point_category_guid' => '2db04693-4eef-11ef-b8f8-075596f1f6fb',
                'user_guid' => $request->user_guid,
            ]);
        }
    
        return ResponseController::getResponse($data, 200, 'Success');
    }

  

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:36',
            'type' => 'required|string',
            'old_km' => 'required|numeric',
            'new_km' => 'required|numeric',
            'files_url.*' => 'nullable|file|mimes:jpg,jpeg,png|max:8192',
            'status'=>'required|string',
            'user_guid' => 'required|uuid|exists:users,guid',
        ], MessagesController::messages());
    
        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
    
        // Get data
        $data = CarbonHistory::where('guid', '=', $request->guid)->first();
    
        if (!$data) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $emissionRates = [
            'mobil' => 0.21,
            'motor' => 0.1,
            'bus' => 0.27
        ];

        $hasil =0;

        $kmdiff = ($request->new_km - $request->old_km);

        if(array_key_exists($request->type, $emissionRates)) {
            $hasil = $emissionRates[$request->type] * $kmdiff;
        } else {
            return 0;
        }
    
        // Update data
        $data->type = $request->type;
        $data->old_km = $request->old_km;
        $data->new_km = $request->new_km;
        $data->km_diff =  $kmdiff;
        $data->carbon_total = $hasil;
        $data->files_url = $request->files_url;
        $data->status = $request->status;
        $data->user_guid = $request->user_guid;
        $data->save();
    
        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function deleteData($guid)
    {
        /// GET DATA
        $data = CarbonHistory::where('guid', '=', $guid)->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $data->delete();

        return ResponseController::getResponse(null, 200, 'Success');
    }
}
