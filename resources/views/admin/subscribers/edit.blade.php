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
                        <label>رقم التعريف الوطني</label>
                        <input type="text" name="national_id" class="form-control" value="{{ $subscriber->national_id }}">
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
                        <input type="email" name="login_email" class="form-control" value="{{ $subscriber->login_email }}" placeholder="email@customer.com" required>
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

                    <div class="col-12">
                        <hr>
                        <h6>{{ __('main.national_address') ?? 'العنوان الوطني' }}</h6>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_short') ?? 'العنوان المختصر' }}</label>
                        <input type="text" name="national_address_short" class="form-control" value="{{ $subscriber->national_address_short }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_building_no') ?? 'رقم المبنى' }}</label>
                        <input type="text" name="national_address_building_no" class="form-control" value="{{ $subscriber->national_address_building_no }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_street') ?? 'الشارع' }}</label>
                        <input type="text" name="national_address_street" class="form-control" value="{{ $subscriber->national_address_street }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_district') ?? 'الحي' }}</label>
                        <input type="text" name="national_address_district" class="form-control" value="{{ $subscriber->national_address_district }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_city') ?? 'المدينة' }}</label>
                        <input type="text" name="national_address_city" class="form-control" value="{{ $subscriber->national_address_city }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_region') ?? 'المنطقة' }}</label>
                        <input type="text" name="national_address_region" class="form-control" value="{{ $subscriber->national_address_region }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_postal_code') ?? 'الرمز البريدي' }}</label>
                        <input type="text" name="national_address_postal_code" class="form-control" value="{{ $subscriber->national_address_postal_code }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_additional_no') ?? 'الرقم الإضافي' }}</label>
                        <input type="text" name="national_address_additional_no" class="form-control" value="{{ $subscriber->national_address_additional_no }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_unit_no') ?? 'رقم الوحدة' }}</label>
                        <input type="text" name="national_address_unit_no" class="form-control" value="{{ $subscriber->national_address_unit_no }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_proof_no') ?? 'رقم إثبات العنوان' }}</label>
                        <input type="text" name="national_address_proof_no" class="form-control" value="{{ $subscriber->national_address_proof_no }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_proof_issue_date') ?? 'تاريخ الإصدار' }}</label>
                        <input type="date" name="national_address_proof_issue_date" class="form-control" value="{{ optional($subscriber->national_address_proof_issue_date)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>{{ __('main.national_address_proof_expiry_date') ?? 'تاريخ الانتهاء' }}</label>
                        <input type="date" name="national_address_proof_expiry_date" class="form-control" value="{{ optional($subscriber->national_address_proof_expiry_date)->format('Y-m-d') }}">
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
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_trial" name="is_trial" value="1" @if($subscriber->is_trial) checked @endif>
                            <label class="form-check-label" for="is_trial">نسخة تجريبية (30 يوماً)</label>
                        </div>
                        <small class="text-muted">تفعيل الخيار يجعل تاريخ النهاية مطابقاً لتاريخ نهاية التجربة.</small>
                    </div>
                    <div class="col-md-3 mb-3 trial-meta d-none">
                        <label>بداية التجربة</label>
                        <input type="date" name="trial_starts_at" class="form-control" value="{{ optional($subscriber->trial_starts_at)->format('Y-m-d') ?? optional($subscriber->subscription_start)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3 trial-meta d-none">
                        <label>نهاية التجربة</label>
                        <input type="date" name="trial_ends_at" class="form-control" value="{{ optional($subscriber->trial_ends_at)->format('Y-m-d') }}" readonly>
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
                                @foreach($subscriber->documents->whereNull('archived_at') as $doc)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">{{ $doc->title ?? basename($doc->file_path) }}</a>
                                        <form action="{{ route('owner.documents.destroy',$doc) }}" method="POST" onsubmit="return confirm('حذف المستند؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">حذف</button>
                                        </form>
                                        <form action="{{ route('owner.documents.archive',$doc) }}" method="POST" onsubmit="return confirm('أرشفة المستند؟');">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary" type="submit">أرشفة</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @if($subscriber->documents->whereNotNull('archived_at')->count())
                            <div class="col-12 mb-3">
                                <label>مستندات مؤرشفة</label>
                                <ul class="list-group">
                                    @foreach($subscriber->documents->whereNotNull('archived_at') as $doc)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $doc->title ?? basename($doc->file_path) }}</span>
                                            <span class="text-muted">مؤرشفة في {{ optional($doc->archived_at)->format('Y-m-d') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
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

    (function(){
        const trialToggle = document.getElementById('is_trial');
        const trialBlocks = document.querySelectorAll('.trial-meta');
        const startInput = document.querySelector('input[name="trial_starts_at"]');
        const endInput = document.querySelector('input[name="trial_ends_at"]');
        const subStartInput = document.querySelector('input[name="subscription_start"]');
        const subEndInput = document.querySelector('input[name="subscription_end"]');

        const formatDate = (date) => date.toISOString().slice(0,10);

        function syncTrial(){
            if (!trialToggle) {
                return;
            }
            const enabled = trialToggle.checked;
            trialBlocks.forEach(block => block.classList.toggle('d-none', !enabled));
            if (enabled) {
                const startVal = startInput.value || subStartInput.value || formatDate(new Date());
                startInput.value = startVal;
                const targetDate = new Date(startVal);
                targetDate.setDate(targetDate.getDate() + 30);
                const endVal = endInput.value || formatDate(targetDate);
                endInput.value = endVal;
                if (subEndInput) {
                    subEndInput.value = endVal;
                    subEndInput.setAttribute('readonly','readonly');
                }
            } else {
                if (subEndInput) {
                    subEndInput.removeAttribute('readonly');
                }
            }
        }

        if (trialToggle) {
            trialToggle.addEventListener('change', syncTrial);
        }
        if (startInput) {
            startInput.addEventListener('change', syncTrial);
        }
        syncTrial();
    })();
</script>
@endsection
