<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $data = Coupon::with('user','offer.merchant_masters')->orderBy('created_at', 'desc')
        ->get();

        return ResponseController::getResponse($data, 200, 'Success');
    }
}
