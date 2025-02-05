@extends('layouts.app')
<!-- DataTables CSS -->

@section('content')
<div class="container">
    <h2>Danh sách Bài viết</h2>
    <!-- Input tìm kiếm -->
    <input type="text" id="keyword" placeholder="Tìm kiếm..." class="form-control mb-2">

    <!-- Bộ lọc danh mục cho danh sách bài viết -->
    <div class="mb-2">
        @foreach($categories as $category)
        <label>
            <input type="checkbox" class="filter-category" value="{{ $category->id }}">
            {{ $category->title }}
        </label>
        @endforeach
    </div>

    <!-- Bảng hiển thị bài viết -->
    <table id="posts-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Danh mục</th>
                <th>Hành động</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Nút mở modal thêm bài viết -->
<button id="addPostBtn" class="btn btn-primary">Thêm Bài viết</button>

<!-- Modal Thêm Bài viết -->
<div id="addPostModal" class="modal" style="display:none;">
    <div class="modal-content"
        style="background: #fff; padding: 20px; border: 1px solid #ddd; max-width: 500px; margin: 50px auto;">
        <div class="modal-header">
            <h5 class="modal-title">Thêm bài viết</h5>
            <!-- Nút đóng modal -->
            <button type="button" class="close" data-dismiss="modal"
                style="border: none; background: none; font-size: 20px;">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="newTitle" placeholder="Tiêu đề" class="form-control mb-2">
            <textarea id="newContent" placeholder="Nội dung" class="form-control mb-2"></textarea>
            <div class="mb-2">
                <p>Chọn danh mục:</p>
                @foreach($categories as $category)
                <label>
                    <input type="checkbox" class="modal-category-filter" value="{{ $category->id }}">
                    {{ $category->title }}
                </label>
                @endforeach
            </div>
        </div>
        <div class="modal-footer">
            <button id="savePostBtn" class="btn btn-success">Lưu</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Định nghĩa hàm loadTable và đặt nó vào phạm vi toàn cục (window)
        window.loadTable = function() {
            // Nếu DataTable đã được khởi tạo, hủy bỏ trước khi khởi tạo lại
            if ($.fn.DataTable.isDataTable('#posts-table')) {
                $('#posts-table').DataTable().destroy();
            }
            $('#posts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("posts.data") }}',
                    data: function(d) {
                        d.keyword = $('#keyword').val();
                        // Lấy danh sách danh mục được chọn từ bộ lọc ngoài modal
                        d.categories = $('.filter-category:checked').map(function() {
                            return this.value;
                        }).get();
                    }
                },
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'content', name: 'content' },
                    { data: 'categories', name: 'categories' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        };

        // Khởi tạo bảng dữ liệu
        loadTable();

        // Lắng nghe sự kiện thay đổi từ input tìm kiếm và checkbox bộ lọc danh mục ngoài modal
        $('#keyword, .filter-category').on('change keyup', function() {
            loadTable();
        });

        // Xử lý sự kiện xóa bài viết (nút có class "delete-btn" được tạo động từ server)
        $(document).on('click', '.delete-btn', function() {
            let postId = $(this).data('id');
            $.ajax({
                url: '/posts/' + postId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Xóa thành công');
                    loadTable();
                },
                error: function(xhr) {
                    toastr.error('Có lỗi xảy ra khi xóa bài viết');
                }
            });
        });

        // Hiển thị modal thêm bài viết khi nhấn nút "Thêm Bài viết"
        $('#addPostBtn').click(function() {
            $('#addPostModal').show();
        });

        // Đóng modal khi nhấn nút đóng (dựa vào attribute data-dismiss="modal" hoặc class .close)
        $(document).on('click', '[data-dismiss="modal"], .close', function() {
            $('#addPostModal').hide();
        });

        // Xử lý lưu bài viết mới
        $('#savePostBtn').click(function() {
            $.ajax({
                url: '{{ route("posts.store") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    title: $('#newTitle').val(),
                    content: $('#newContent').val(),
                    // Lấy danh mục được chọn trong modal
                    categories: $('#addPostModal .modal-category-filter:checked').map(function() {
                        return this.value;
                    }).get()
                },
                success: function(response) {
                    toastr.success('Thêm bài viết thành công!');
                    $('#addPostModal').hide();
                    loadTable();
                },
                error: function(xhr) {
                    toastr.error('Có lỗi xảy ra khi thêm bài viết');
                }
            });
        });
    });
</script>
@endsection