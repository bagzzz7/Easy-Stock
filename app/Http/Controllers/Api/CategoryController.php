<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Get all categories
     * GET /api/categories
     */
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
