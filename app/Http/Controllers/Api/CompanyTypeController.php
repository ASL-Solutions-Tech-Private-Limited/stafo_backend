<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CompanyTypeController extends Controller
{
    // Method for listing company types
    public function index()
    {
        try {

            $companyTypes = CompanyType::all();

            return response()->json([
                'success' => true,
                'data' => $companyTypes
            ], Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve company types.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Method for adding a new company type
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);


            if ($validator->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }


            $companyType = CompanyType::create([
                'company_name' => $request->company_name,
                'status' => $request->status,
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Company type created successfully.',
                'data' => $companyType
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to create company type.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}