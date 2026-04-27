<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DcCcBbpsCategory;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BbpsCategoryController extends Controller
{


    public function index()
    {
        // dd("test");
        $categories = DcCcBbpsCategory::where('status', 1)
            ->select('id', 'name', 'code', 'img', 'status')
            ->get()
            ->map(function ($item) {
                $item->icon_url = $item->img
                    ? asset('uploads/bbps_category/' . $item->img)
                    : null;
                return $item;
            });


        return response()->json([
            'success' => true,
            'data'    => $categories
        ], 200);
    }



    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'img'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = null;

            if ($request->hasFile('img')) {
                $image      = $request->file('img');
                $imageName  = Str::slug($request->name ?? 'category') . '_' . time() . '.' . $image->getClientOriginalExtension();


                $folderPath = public_path('uploads/bbps_category');

                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true);
                }

                $image->move($folderPath, $imageName);
            }
            $category = new DcCcBbpsCategory();
            $category->name = $request->name;
            $category->code = $request->name;
            $category->img  = $imageName;
            $category->save();

            return response()->json([
                'success' => true,
                'data'    => $category
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 201);
        }
    }



    public function update(Request $request, $id)
    {
        $category = DcCcBbpsCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'img'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = $category->img;

            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imageName = Str::slug($request->name ?? $category->name) . '_' . time() . '.' . $image->getClientOriginalExtension();

                $folderPath = public_path('uploads/bbps_category');

                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true);
                }

                $image->move($folderPath, $imageName);

                // delete old image if exists
                if ($category->img && File::exists($folderPath . '/' . $category->img)) {
                    File::delete($folderPath . '/' . $category->img);
                }
            }

            $category->name = $request->input('name', $category->name);
            $category->code = $request->input('name', $category->code);
            $category->img  = $imageName;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data'    => $category
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 201);
        }
    }


    public function destroy($id)
    {
        $category = DcCcBbpsCategory::find($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        if ($category->img && File::exists(public_path('uploads/bbps_category/' . $category->img))) {
            File::delete(public_path('uploads/bbps_category/' . $category->img));
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully'], 200);
    }
}