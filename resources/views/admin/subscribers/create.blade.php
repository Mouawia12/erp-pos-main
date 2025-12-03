@extends('admin.layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">اضافة مشترك جديد</h4>
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
            <form method="POST" action="{{ route('owner.subscribers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>اسم الشركة *</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>رقم السجل التجاري</label>
                        <input type="text" name="cr_number" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>الرقم الضريبي</label>
                        <input type="text" name="tax_number" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>الشخص المسؤول</label>
                        <input type="text" name="responsible_person" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>البريد</label>
                        <input type="email" name="contact_email" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>الهاتف</label>
                        <input type="text" name="contact_phone" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>بريد دخول المشترك</label>
                        <input type="email" name="login_email" class="form-control" placeholder="email@customer.com" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>كلمة مرور المشترك (اختياري)</label>
                        <input type="text" name="login_password" class="form-control" placeholder="اتركها فارغة لتوليد كلمة مرور افتراضية">
                        <small class="text-muted">الافتراضي: password123</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>العنوان</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>رابط النظام (للقراءة فقط)</label>
                        <input type="text" name="system_url" class="form-control" placeholder="https://saas.example.com/company">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>حد المستخدمين</label>
                        <input type="number" name="users_limit" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>بداية الاشتراك</label>
                        <input type="date" name="subscription_start" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>نهاية الاشتراك</label>
                        <input type="date" name="subscription_end" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_trial" name="is_trial" value="1">
                            <label class="form-check-label" for="is_trial">تفعيل نسخة تجريبية (30 يوماً)</label>
                        </div>
                        <small class="text-muted">سيتم تعبئة تاريخ النهاية تلقائياً وإيقاف الحساب بعد 30 يوماً ما لم يتم تجديده.</small>
                    </div>
                    <div class="col-md-3 mb-3 trial-meta d-none">
                        <label>بداية التجربة</label>
                        <input type="date" name="trial_starts_at" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3 trial-meta d-none">
                        <label>نهاية التجربة</label>
                        <input type="date" name="trial_ends_at" class="form-control" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="1"></textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label>مستندات (CR / رخص / عقود) - يمكن رفع اكثر من ملف</label>
                        <div id="docs-wrapper">
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" name="document_titles[]" class="form-control" placeholder="عنوان المستند (اختياري)">
                                <input type="file" name="documents[]" class="form-control" accept="image/*,application/pdf">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDocRow()">اضافة مستند</button>
                    </div>
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
                const endVal = formatDate(targetDate);
                endInput.value = endVal;
                if (subEndInput) {
                    subEndInput.value = endVal;
                    subEndInput.setAttribute('readonly', 'readonly');
                }
            } else {
                startInput.value = '';
                endInput.value = '';
                if (subEndInput) {
                    subEndInput.removeAttribute('readonly');
                }
            }
        }

        ['change','keyup'].forEach(evt => {
            if (startInput) {
                startInput.addEventListener(evt, syncTrial);
            }
        });
        if (trialToggle) {
            trialToggle.addEventListener('change', syncTrial);
        }
        syncTrial();
    })();
</script>
@endsection
