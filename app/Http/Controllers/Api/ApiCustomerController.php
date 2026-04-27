<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class ApiCustomerController extends Controller
{
    public function getCustomerList(){
        $customer = Customer::get();

        return response()->json(['user_list' => $customer]);
    }
}