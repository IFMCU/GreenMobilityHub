<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MessagesController;
use App\Models\PointHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class PointHistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Index method called with request:', $request->all());
    
            // Fetch the data from the database
            $data = PointHistory::with('point_categories')
                ->orderBy('created_at', 'desc')
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
        $data = PointHistory::where('guid', '=', $guid)
            ->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function getAllDataTable()
    {
        $data = PointHistory::with('point_categories')
            ->get();

        $dataTable = DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        return $dataTable;
    }

    public function insertData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'point' => 'required|numeric',
            'file_url' => 'required|string',
            'point_category_guid' => 'required|uuid|exists:point_categories,guid',
            'user_guid' => 'required|uuid|exists:users,guid',
            'status'=>'required|string',
        ], MessagesController::messages());
    
        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
    
        $data = PointHistory::create([
            'point' => $request->point,
            'file_url' => $request->file_url,
            'point_category_guid' => $request->point_category_guid,
            'user_guid' => $request->user_guid,
            'status' => $request->status,
        ]);
    
        return ResponseController::getResponse($data, 200, 'Success');
    }

  

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:36',
            'point' => 'required|numeric',
            'file_url' => 'required|string',
            'point_category_guid' => 'required|uuid|exists:point_categories,guid',
            'user_guid' => 'required|uuid|exists:users,guid',
            'status'=>'required|string',
        ], MessagesController::messages());
    
        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
    
        // Get data
        $data = PointHistory::where('guid', '=', $request->guid)->first();
    
        if (!$data) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }
    
        // Update data
        $data->point = $request->point;
        $data->file_url = $request->file_url;
        $data->point_category_guid = $request->point_category_guid;
        $data->km_diff = $request->km_diff;
        $data->user_guid = $request->user_guid;
        $data->status = $request->status;
        $data->save();
    
        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function deleteData($guid)
    {
        /// GET DATA
        $data = PointHistory::where('guid', '=', $guid)->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $data->delete();

        return ResponseController::getResponse(null, 200, 'Success');
    }
}
