<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class OfferController extends Controller
{
    public function index(Request $request)
    {

        $data = Offer::with('merchant_master')->orderBy('created_at', 'desc')
        ->get();

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function getData($guid)
    {
        /// GET DATA
        $data = Offer::with('merchant_master')->where('guid', '=', $guid)
            ->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function getAllDataTable()
    {
        $data = Offer::with('merchant_master')->orderBy('created_at', 'desc')
            ->get();

        $dataTable = DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        return $dataTable;
    }

    public function insertData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'stock' => 'required|integer',
            'point' => 'required|integer',
            'files_url.*' => 'nullable|file|mimes:jpg,jpeg,png|max:8192',
            'description' => 'required|integer',
            'merchant_master_guid' => 'required|string|max:36'
        ], MessagesController::messages());

        $files_url = $request->hasFile('files_url') ? $request->file('files_url')->store('attachments', 'public') : null;

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $data = Offer::create([
            'name' => $request['name'],
            'stock' => $request['stock'],
            'point' => $request['point'],
            'description' => $request['description'],
            'merchant_master_guid' => $request['merchant_master_guid'],
            'files_url' => $files_url
        ]);

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:36',
            'name' => 'required|string',
            'stock' => 'required|integer',
            'point' => 'required|integer',
            'files_url.*' => 'nullable|file|mimes:jpg,jpeg,png|max:8192',
            'description' => 'required|integer',
            'merchant_master_guid' => 'required|string|max:36'
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        /// GET DATA
        $data = Offer::where('guid', '=', $request['guid'])->first();

        $files_url = $data->files_url;

        if ($request->has('delete_file') && $request->delete_file == 1) {
            if ($data->files_url) {
                unlink("storage/" . $data->files_url);
                $files_url = null;
            }
        } elseif ($request->hasFile('files_url')) {
            if ($data->files_url) {
                unlink("storage/" . $data->files_url);
            }

            
            $files_url = $request->file('files_url')->store('attachments', 'public');
        }


        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        /// UPDATE DATA
        $data->name = $request['name'];
        $data->stock = $request['stock'];
        $data->point = $request['point'];
        $data->description = $request['description'];
        $data->merchant_master_guid = $request['merchant_master_guid'];
        $data->files_url = $files_url;
        $data->save();

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function deleteData($guid)
    {
        /// GET DATA
        $data = Offer::where('guid', '=', $guid)->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $data->delete();

        return ResponseController::getResponse(null, 200, 'Success');
    }
}
