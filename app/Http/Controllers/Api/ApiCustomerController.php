<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class ApiCustomerController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/getcustomerlist",
     *     summary="Get customer list",
     *     tags={"Customer"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Customer list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_list", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getCustomerList(){
        $customer = Customer::get();

        return response()->json(['user_list' => $customer]);
    }
}