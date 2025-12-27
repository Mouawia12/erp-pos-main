@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">لوحة المشتركين (مالك النظام)</h4>
                <a href="{{ route('owner.subscribers.create') }}" class="btn btn-primary">اضافة مشترك</a>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3 mb-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">اجمالي</h6>
                        <h4>{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h6 class="text-muted">نشط</h6>
                        <h4 class="text-success">{{ $stats['active'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <h6 class="text-muted">قرب الانتهاء</h6>
                        <h4 class="text-warning">{{ $stats['near_expiry'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-center border-danger">
                    <div class="card-body">
                        <h6 class="text-muted">منتهي</h6>
                        <h4 class="text-danger">{{ $stats['expired'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>الشركة</th>
                            <th>بيانات تواصل</th>
                            <th>الرابط / الوصول</th>
                            <th>بيانات دخول المشترك</th>
                            <th>المستخدمون</th>
                            <th>مدة الاشتراك</th>
                            <th>الحالة</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td>
                                    <strong>{{ $subscriber->company_name }}</strong>
                                    @if($subscriber->is_trial)
                                        <span class="badge badge-info ms-1">نسخة تجريبية</span>
                                    @endif
                                    <br>
                                    <small>س.ت: {{ $subscriber->cr_number ?? '-' }} | ضريبة: {{ $subscriber->tax_number ?? '-' }}</small><br>
                                    <small>مسؤول: {{ $subscriber->responsible_person ?? '-' }}</small>
                                </td>
                                <td>
                                    <div>{{ $subscriber->contact_phone ?? '-' }}</div>
                                    <div>{{ $subscriber->contact_email ?? '-' }}</div>
                                    <div>{{ $subscriber->address ?? '' }}</div>
                                </td>
                                <td>
                                    @if($subscriber->system_url)
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="text" class="form-control" value="{{ $subscriber->system_url }}" readonly id="link-{{ $subscriber->id }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" onclick="copyLink({{ $subscriber->id }})">نسخ</button>
                                            </div>
                                        </div>
                                        <a href="{{ $subscriber->system_url }}" target="_blank">الدخول للعميل (قراءة فقط)</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div>بريد: {{ $subscriber->login_email ?? '-' }}</div>
                                    <div>كلمة المرور: {{ $subscriber->login_password_plain ?? '-' }}</div>
                                </td>
                                <td>
                                    @forelse($subscriber->users as $user)
                                        <div>{{ $user->name }} - {{ $user->email }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    <div>من: {{ optional($subscriber->subscription_start)->format('Y-m-d') ?? '-' }}</div>
                                    <div>إلى: {{ optional($subscriber->subscription_end)->format('Y-m-d') ?? '-' }}</div>
                                    <small>المستخدمون المسموحون: {{ $subscriber->users_limit }}</small>
                                    @php $days = $subscriber->remainingDays(); @endphp
                                    @if($days !== null)
                                        <div class="text-muted">متبقي: {{ $days }} يوم</div>
                                    @endif
                                </td>
                                <td>
                                    @php $badge = 'secondary';
                                        if($subscriber->status === 'active') $badge = 'success';
                                        elseif($subscriber->status === 'near_expiry') $badge = 'warning';
                                        elseif($subscriber->status === 'expired') $badge = 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ $subscriber->status }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('owner.subscribers.edit', $subscriber) }}" class="btn btn-sm btn-info mb-1">تعديل</a>
                                    <form action="{{ route('owner.subscribers.destroy', $subscriber) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف المشترك؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger mb-1" type="submit">حذف</button>
                                    </form>
                                    <a href="{{ route('owner.subscribers.permissions', $subscriber) }}" class="btn btn-sm btn-secondary mb-1">
                                        تعديل الصلاحيات
                                    </a>
                                    <form action="{{ route('owner.subscribers.renew', $subscriber) }}" method="POST" class="d-flex flex-column gap-1" style="min-width:180px">
                                        @csrf
                                        <div class="d-flex gap-1">
                                            <input type="number" name="add_days" class="form-control form-control-sm" placeholder="ايام" min="0">
                                            <input type="number" name="add_months" class="form-control form-control-sm" placeholder="اشهر" min="0">
                                            <input type="number" name="add_years" class="form-control form-control-sm" placeholder="سنوات" min="0">
                                        </div>
                                        <input type="text" name="notes" class="form-control form-control-sm mb-1" placeholder="ملاحظة التجديد">
                                        <button class="btn btn-sm btn-success">تجديد سريع</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">لا يوجد مشتركون بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
</div>
@endsection

@section('js')
<script>
    function copyLink(id){
        const input = document.getElementById('link-'+id);
        input.select();
        document.execCommand('copy');
    }
</script>
@endsection
