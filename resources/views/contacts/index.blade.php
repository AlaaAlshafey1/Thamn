@extends('layouts.master')
@section('title', 'جهات الاتصال')

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary">إدارة جهات الاتصال</h4>
        <small class="text-muted">عرض جميع جهات الاتصال والتحكم بها</small>
    </div>

<div class="d-flex flex-wrap justify-content-start gap-2">
    @if(\App\Models\Contact::count() == 0)
        <a href="{{ route('contacts.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="background-color:#c1953e; border-color:#c1953e;">
            <i class="bx bx-plus-circle fs-5"></i> <span>إضافة جهة اتصال جديدة</span>
        </a>
    @endif
</div>

</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">قائمة جهات الاتصال</h5>
        <small class="text-muted">عرض جميع جهات الاتصال المسجلة</small>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table id="contactsTable" class="table table-hover table-striped text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>وسائل التواصل</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contacts as $key => $contact)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $contact->phone ?? '-' }}</td>
                            <td>{{ $contact->email ?? '-' }}</td>
                            <td>
                                @php
                                    $socials = [];
                                    if($contact->social_media) {
                                        if(is_string($contact->social_media)) {
                                            $socials = json_decode($contact->social_media, true) ?? [];
                                        } elseif(is_array($contact->social_media)) {
                                            $socials = $contact->social_media;
                                        }
                                    }
                                @endphp

                                @if(count($socials))
                                    @foreach($socials as $sm)
                                        <a href="{{ $sm['url'] }}" target="_blank" class="badge bg-info text-dark mb-1">{{ $sm['name'] }}</a>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>

                                    <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
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
            {{ $contacts->links() }}
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#contactsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
        pageLength: 10,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"btn-left"B><"search-box"f>>rtip',
        buttons: [
            { extend: 'copy', text: '📋 نسخ', className: 'btn-sm mx-1' },
            { extend: 'excel', text: '📊 Excel', className: 'btn-sm mx-1' },
            { extend: 'pdf', text: '📄 PDF', className: 'btn-sm mx-1' },
            { extend: 'print', text: '🖨️ طباعة', className: 'btn-sm mx-1' }
        ]
    });
});
</script>
@endsection
