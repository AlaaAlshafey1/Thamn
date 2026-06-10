@extends('layouts.master')
@section('title', 'الفئات')

@section('css')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
    }

    /* Drag & Drop styles */
    .sortable-row {
        cursor: grab;
        transition: background-color 0.2s ease;
    }
    .sortable-row:active {
        cursor: grabbing;
    }
    .sortable-ghost {
        opacity: 0.4;
        background-color: #fff3cd !important;
    }
    .sortable-chosen {
        background-color: #f8f0e0 !important;
        box-shadow: 0 4px 12px rgba(193, 149, 62, 0.3);
    }
    .drag-handle {
        cursor: grab;
        color: #aaa;
        font-size: 18px;
        padding: 0 8px;
    }
    .drag-handle:hover {
        color: #c1953e;
    }
    .sortable-row:active .drag-handle {
        cursor: grabbing;
    }

    /* Save order button — hidden by default */
    #saveOrderBtn {
        display: none !important;
    }
    #saveOrderBtn.visible {
        display: inline-flex !important;
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
        animation: fadeIn 0.3s ease;
    }
    #saveOrderBtn.visible:hover {
        background-color: #218838;
        border-color: #218838;
        color: #fff;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Toast notification */
    .order-toast {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        z-index: 9999;
        padding: 12px 24px;
        border-radius: 10px;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        transition: transform 0.4s ease;
    }
    .order-toast.show {
        transform: translateX(-50%) translateY(0);
    }
    .order-toast.success { background-color: #28a745; }
    .order-toast.error { background-color: #dc3545; }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary">إدارة الفئات</h4>
        <small class="text-muted">عرض جميع الفئات والتحكم بها — اسحب وأفلت لإعادة الترتيب</small>
    </div>

    <div class="d-flex flex-wrap justify-content-start gap-2">
        <button type="button" id="saveOrderBtn" class="btn btn-sm align-items-center gap-1">
            <i class="bx bx-save fs-5"></i> <span>حفظ الترتيب</span>
        </button>
        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="background-color:#c1953e; border-color:#c1953e;">
            <i class="bx bx-plus-circle fs-5"></i> <span>إضافة فئة جديدة</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Toast notification -->
<div id="orderToast" class="order-toast"></div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">قائمة الفئات</h5>
        <small class="text-muted">اسحب الصفوف لإعادة ترتيب الفئات — الترتيب سيظهر في التطبيق</small>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-striped text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px;">ترتيب</th>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الوصف</th>
                        <th>الحالة</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody id="sortableBody">
                    @foreach($categories as $key => $category)
                        <tr class="sortable-row" data-id="{{ $category->id }}">
                            <td>
                                <span class="drag-handle" title="اسحب لإعادة الترتيب">
                                    <i class="bx bx-menu"></i>
                                </span>
                            </td>
                            <td class="row-number">{{ $key + 1 }}</td>
                            <td>{{ $category->name_ar }}</td>
                            <td>{{ $category->description_ar ?? '-' }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">مفعّل</span>
                                @else
                                    <span class="badge bg-danger">غير مفعّل</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>

                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
$(document).ready(function() {
    var orderChanged = false;
    var saveBtn = document.getElementById('saveOrderBtn');
    var toastEl = document.getElementById('orderToast');
    var sortableEl = document.getElementById('sortableBody');

    if (!sortableEl) {
        console.error('sortableBody element not found!');
        return;
    }

    // Initialize SortableJS on the table body
    try {
        var sortable = Sortable.create(sortableEl, {
            handle: '.drag-handle',
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                console.log('Drag ended. Old index:', evt.oldIndex, 'New index:', evt.newIndex);
                orderChanged = true;
                saveBtn.classList.add('visible');
                updateRowNumbers();
            }
        });
        console.log('SortableJS initialized successfully');
    } catch(e) {
        console.error('SortableJS initialization error:', e);
    }

    // Update row numbers after drag
    function updateRowNumbers() {
        $('#sortableBody .sortable-row').each(function(index) {
            $(this).find('.row-number').text(index + 1);
        });
    }

    // Show toast notification
    function showToast(message, type) {
        toastEl.textContent = message;
        toastEl.className = 'order-toast ' + type;
        setTimeout(function() { toastEl.classList.add('show'); }, 10);
        setTimeout(function() {
            toastEl.classList.remove('show');
        }, 3000);
    }

    // Save order via AJAX
    $(saveBtn).on('click', function() {
        if (!orderChanged) return;

        var order = [];
        $('#sortableBody .sortable-row').each(function() {
            order.push(parseInt($(this).data('id')));
        });

        console.log('Saving order:', order);

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin fs-5"></i> <span>جاري الحفظ...</span>';

        $.ajax({
            url: '{{ route("categories.reorder") }}',
            type: 'POST',
            data: JSON.stringify({ order: order }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Reorder success:', response);
                orderChanged = false;
                saveBtn.classList.remove('visible');
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bx bx-save fs-5"></i> <span>حفظ الترتيب</span>';
                showToast('✅ ' + response.message, 'success');
            },
            error: function(xhr, status, error) {
                console.error('Reorder error:', status, error, xhr.responseText);
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bx bx-save fs-5"></i> <span>حفظ الترتيب</span>';
                showToast('❌ حدث خطأ أثناء حفظ الترتيب', 'error');
            }
        });
    });
});
</script>
@endsection
