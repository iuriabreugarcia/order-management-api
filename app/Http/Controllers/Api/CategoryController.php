<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
	$categories = Category::query()
            ->orderBy('name')
            ->paginate(10);

	return CategoryResource::collection($categories)->response();

    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $category = Category::create($validated);

        return (new CategoryResource($category))
    		->response()
    		->setStatusCode(201);
    	}

    public function show(Category $category): JsonResponse
    {
        return (new CategoryResource(
    		$category->load('products')
	))->response();
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $validated = $request->validated();

        $category->update($validated);

        return (new CategoryResource($category))->response();
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Category cannot be deleted because it has associated products.',
            ], 409);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}