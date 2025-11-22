@extends('admin.layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">تعديل مشترك</h4>
            <a href="{{ route('owner.subscribers.index') }}" class="btn btn-secondary">عودة</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('owner.subscribers.update',$subscriber) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>اسم الشركة *</label>
                        <input type="text" name="company_name" class="form-control" value="{{ $subscriber->company_name }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>رقم السجل التجاري</label>
                        <input type="text" name="cr_number" class="form-control" value="{{ $subscriber->cr_number }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>الرقم الضريبي</label>
                        <input type="text" name="tax_number" class="form-control" value="{{ $subscriber->tax_number }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>الشخص المسؤول</label>
                        <input type="text" name="responsible_person" class="form-control" value="{{ $subscriber->responsible_person }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>البريد</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $subscriber->contact_email }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>الهاتف</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ $subscriber->contact_phone }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>بريد دخول المشترك</label>
                        <input type="email" name="login_email" class="form-control" value="{{ $subscriber->login_email }}" placeholder="email@customer.com">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>كلمة مرور المشترك (اختياري للتغيير)</label>
                        <input type="text" name="login_password" class="form-control" placeholder="اتركها فارغة للإبقاء على الحالية">
                        @if($subscriber->login_password_plain)
                            <small class="text-muted">الحالية: {{ $subscriber->login_password_plain }}</small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>العنوان</label>
                        <input type="text" name="address" class="form-control" value="{{ $subscriber->address }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>رابط النظام (للقراءة فقط)</label>
                        <input type="text" name="system_url" class="form-control" value="{{ $subscriber->system_url }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>حد المستخدمين</label>
                        <input type="number" name="users_limit" class="form-control" value="{{ $subscriber->users_limit }}" min="1">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>بداية الاشتراك</label>
                        <input type="date" name="subscription_start" class="form-control" value="{{ optional($subscriber->subscription_start)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>نهاية الاشتراك</label>
                        <input type="date" name="subscription_end" class="form-control" value="{{ optional($subscriber->subscription_end)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="1">{{ $subscriber->notes }}</textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label>مستندات جديدة (اختياري)</label>
                        <div id="docs-wrapper">
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" name="document_titles[]" class="form-control" placeholder="عنوان المستند (اختياري)">
                                <input type="file" name="documents[]" class="form-control" accept="image/*,application/pdf">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDocRow()">اضافة مستند</button>
                    </div>

                    @if($subscriber->documents->count())
                        <div class="col-12 mb-3">
                            <label>المستندات الحالية</label>
                            <ul class="list-group">
                                @foreach($subscriber->documents as $doc)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">{{ $doc->title ?? basename($doc->file_path) }}</a>
                                        <form action="{{ route('owner.documents.destroy',$doc) }}" method="POST" onsubmit="return confirm('حذف المستند؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">حذف</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <button class="btn btn-success" type="submit">حفظ</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function addDocRow(){
        const wrapper = document.getElementById('docs-wrapper');
        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-2';
        div.innerHTML = '<input type="text" name="document_titles[]" class="form-control" placeholder="عنوان المستند (اختياري)">\
                         <input type="file" name="documents[]" class="form-control" accept="image/*,application/pdf">';
        wrapper.appendChild(div);
    }
</script>
@endsection
