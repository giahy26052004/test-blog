<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use DataTables;

class PostController extends Controller
{
    public function index()
    {
        // Truyền danh sách danh mục để hiển thị bộ lọc
        $categories = Category::all();
        return view('posts.index', compact('categories'));
    }

    public function data(Request $request)
    {
        $query = Post::with('categories');

        // Tìm kiếm theo từ khóa
        if ($request->keyword) {
            $query->where('title', 'like', "%{$request->keyword}%")
                ->orWhere('content', 'like', "%{$request->keyword}%");
        }

        // Lọc theo danh mục
        if ($request->categories) {
            $categoryIds = $request->categories;
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        return DataTables::eloquent($query)
            ->addColumn('categories', function (Post $post) {
                return $post->categories->pluck('title')->join(', ');
            })
            ->addColumn('action', function (Post $post) {
                $editUrl = route('posts.edit', $post->id);
                return '<a href="' . $editUrl . '" class="btn btn-sm btn-info">Sửa</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $post->id . '">Xóa</button>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'title' => 'required|string',
            'content' => 'required',
            'categories' => 'required|array',
        ]);

        $post = Post::create([
            'title'   => $request->title,
            'content' => $request->content,
            'views'   => 0,
        ]);

        // Gán danh mục
        $post->categories()->attach($request->categories);

        return response()->json(['message' => 'Thêm bài viết thành công']);
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required',
            'categories' => 'required|array',
        ]);

        $post->update([
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        // Đồng bộ lại các danh mục của bài viết
        $post->categories()->sync($request->categories);

        return response()->json(['message' => 'Cập nhật bài viết thành công']);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Xóa bài viết thành công']);
    }
}
