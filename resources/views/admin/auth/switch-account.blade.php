@extends('admin.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h4 class="mb-3">أنت مسجل الدخول بالفعل</h4>
                        <p class="text-muted mb-4">
                            الحساب الحالي: <strong>{{ $user->name }}</strong> ({{ $user->email }})
                        </p>
                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ route('admin.home') }}" class="btn btn-primary">
                                الانتقال للوحة التحكم الحالية
                            </a>
                        </div>
                        <p class="mb-2">هل ترغب في استخدام حساب مالك أو عميل آخر؟</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.login', ['switch' => 1]) }}" class="btn btn-outline-danger">
                                تسجيل الخروج والدخول بحساب مختلف
                            </a>
                        </div>
                        <p class="text-muted mt-3 mb-0">
                            سيتم تسجيل خروج الجلسة الحالية قبل عرض نموذج الدخول الجديد.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
