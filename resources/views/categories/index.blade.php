@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách Danh mục</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('categories.create') }}" class="btn btn-primary mb-2">Thêm Danh mục</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Số View</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td><a href="{{ route('categories.edit', $category->id) }}">{{ $category->title }}</a></td>
                <td>{{ $category->views }}</td>
                <td>
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Hiển thị phân trang -->
    {{ $categories->links() }}
</div>
@endsection