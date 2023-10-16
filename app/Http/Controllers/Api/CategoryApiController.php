<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Category::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'percentage' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $data = $request->all();
            $photo = $request->file('image');

            // $cover_name = $request->file('image')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('image')->getClientOriginalName();
            $cover_name = str_replace(" ", "", $orgFileName);
            $new_photo = time() . $cover_name;


            $destinationPathCover = public_path() . '/images/category/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }

            $data['image'] = $new_photo;
            $photo->move($destinationPathCover, $new_photo);
            $category = Category::create($data);

            if ($category) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Category created successfully',
                    'data' => $category,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Category',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'percentage' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $imagePath = public_path("images/category/" . $category->image);
            unlink($imagePath);

            $data = $request->all();
            $photo = $request->file('image');
            // $cover_name = $request->file('image')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('image')->getClientOriginalName();
            $cover_name = str_replace(" ", "", $orgFileName);
            $new_photo = time() . $cover_name;
            // return $cover_name;
            $destinationPathCover = public_path() . '/images/category/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }

            $data['image'] = $new_photo;
            $photo->move($destinationPathCover, $new_photo);
            $category->update($data);

            return response()->json(['message' => 'Category updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        if ($category) {
            $imagePath = public_path("images/category/" . $category->image);
            unlink($imagePath);
            return response()->json([
                'status' => 200,
                'message' => 'Category delete successfully',
                'data' => $category,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete Category',
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $name = $request->input('name');
        $category = Category::where('name', 'like', '%' . $name . '%')->get();
        return $category;


        if ($category) {
            return response()->json([
                'status' => 200,
                'message' => 'Category search successfully',
                'data' => $category,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to search Category',
            ], 500);
        }
    }

    public function getCategoryList(Request $request)
    {
        $start = $request->gridState['skip'];
        $take = $request->gridState['take'];
        $DisplayStart = 1;
        if (isset($start))
            $DisplayStart = intval($start);

        $DisplayLength = 10;
        if (isset($take))
            $DisplayLength = intval($take);

        $sortingName = '';
        $sortBy = '';
        $sortField = '';

        if (isset($request->gridState['sort'][0]['dir'])) {
            if (count($request->gridState['sort']) > 0) {
                $sort = $request->gridState['sort'][0];
                $sortBy = $sort['dir'] == null ? $sortBy : $sort['dir'];
                $sortField = $sort['field'];
            }
            //echo $sortField;exit();
            if (in_array($sortField, array('name', 'percentage'))) {
                if ($sortBy == 'desc') {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                } else {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                }
            }
        } else //default sorting
        {
            $sortingName = 'name';
            $sortBy = 'asc';
        }

        $filterOptions = $request->gridState['filter']['filters'];
        if (count($filterOptions) > 0) {
            for ($i = 0; $i < count($filterOptions); $i++) {
                $fieldName = $filterOptions[$i]['field'];
                $fieldvalue = $filterOptions[$i]['value'];
                $FilterBy = '';

                if ($fieldName == 'name') {
                    $fieldName = $fieldName;
                    $fieldvalue = '%' . $fieldvalue . '%';
                    $FilterBy = 'LIKE';
                }
                if($fieldName == 'percentage'){
                    $fieldName = $fieldName;
                    $fieldvalue = $fieldvalue;
                    $FilterBy = '=';
                }
            }
        }

        $qryResult = null;

        if (isset($fieldName)) {
            $qryResult = Category::where($fieldName, $FilterBy, $fieldvalue)
                ->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        } else {
            $qryResult = Category::orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        }

        $categories = Category::all();
        $dataRowsCount = count($categories);
        return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }

    public function getCategories()
    {
        $res_category = Category::all();
        return response()->json(['data' => $res_category]);
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('files');

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images/category'), $file_name);

            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }

    public function saveCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image_name' => 'required',
            'percentage' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $category = Category::create([
                'name' => $request->input('name'),
                'image' => $request->input('image_name'),
                'percentage' => $request->input('percentage')
            ]);

            if ($category) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Category created successfully',
                    'data' => $category,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Currency',
                ], 500);
            }
        }
    }

    public function updateCategory(Request $request, Category $category)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image_name' => 'required',
            'percentage' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }

        $category = Category::find($request->id);

        $category->name = $request->input('name');
        $category->image = $request->input('image_name');
        $category->percentage = $request->input('percentage');

        if ($request->image_name !== $request->old_image && $request->old_image !== null)
            unlink(public_path('images/category/' . $request->old_image));

        $updated = $category->save();

        if ($updated) {
            return response()->json([
                'status' => 200,
                'message' => 'Category updated successfully',
                'data' => $category,
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update Category',
            ]);
        }
    }

    public function removeCategory(Request $request)
    {
        $category_id = $request->id;
        $result = Category::where('id', $category_id)->delete();
        if ($result)
            unlink(public_path('images/category/' . $request->image));
        return response()->json(['data' => $result]);
    }
}
