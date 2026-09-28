<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DcCcBbpsSubCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BbpsSubCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/bbps-sub-category/list",
     *     summary="Get BBPS sub categories list",
     *     tags={"BBPS"},
     *     @OA\Response(
     *         response=200,
     *         description="Sub category list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sub category list fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $subCategories = DcCcBbpsSubCategory::all()->map(function ($item) {
            $item->img_url = $item->img ? asset('uploads/bbps_sub_category/' . $item->img) : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Sub category list fetched successfully.',
            'data'    => $subCategories
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/bbps-sub-category/create",
     *     summary="Create a BBPS sub category",
     *     tags={"BBPS"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category","name"},
     *                 @OA\Property(property="category", type="string", example="Electricity"),
     *                 @OA\Property(property="name", type="string", example="North Bihar Power"),
     *                 @OA\Property(property="img", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'category' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'img'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = null;

            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imageName = Str::slug($request->name) . '_' . time() . '.' . $image->getClientOriginalExtension();

                $folderPath = public_path('uploads/bbps_sub_category');

                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true);
                }

                $image->move($folderPath, $imageName);
            }

            $subCategory = DcCcBbpsSubCategory::create([
                'category' => $request->category,
                'name'     => $request->name,
                'code'     => $request->name,
                'img'      => $imageName,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $subCategory
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 201);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/bbps-sub-category/update/{id}",
     *     summary="Update a BBPS sub category",
     *     tags={"BBPS"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Sub Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="category", type="string", example="Electricity"),
     *                 @OA\Property(property="name", type="string", example="South Bihar Power"),
     *                 @OA\Property(property="img", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sub-category updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $subCategory = DcCcBbpsSubCategory::find($id);

        if (!$subCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Sub-category not found.'
            ], 404);
        }

        try {
            $request->validate([
                'category' => 'nullable|string|max:50',
                'name'     => 'nullable|string|max:255',
                'img'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = $subCategory->img;

            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imageName = Str::slug($request->name ?? $subCategory->name) . '_' . time() . '.' . $image->getClientOriginalExtension();

                $folderPath = public_path('uploads/bbps_sub_category');
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true);
                }

                $image->move($folderPath, $imageName);

                // Optionally delete old image
                if ($subCategory->img && File::exists($folderPath . '/' . $subCategory->img)) {
                    File::delete($folderPath . '/' . $subCategory->img);
                }
            }

            $subCategory->update([
                'category' => $request->input('category', $subCategory->category),
                'name'     => $request->input('name', $subCategory->name),
                'code'     => $request->input('name', $subCategory->code),
                'img'      => $imageName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sub-category updated successfully.',
                'data'    => $subCategory
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 201);
        }
    }



    /**
     * @OA\Delete(
     *     path="/api/bbps-sub-category/delete/{id}",
     *     summary="Delete a BBPS sub category",
     *     tags={"BBPS"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Sub Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="sub Category deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $subCategory = DcCcBbpsSubCategory::find($id);

        if (!$subCategory) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        if ($subCategory->img && File::exists(public_path('uploads/bbps_sub_category/' . $subCategory->img))) {
            File::delete(public_path('uploads/bbps_sub_category/' . $subCategory->img));
        }
        $subCategory->delete();

        return response()->json(['success' => true, 'message' => 'sub Category deleted successfully']);
    }
}