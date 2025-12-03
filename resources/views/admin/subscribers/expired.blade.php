@extends('admin.layouts.master')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h5 class="mb-0">تم إيقاف الحساب مؤقتاً</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="lead mb-3">
                            انتهت مدة الاشتراك الخاصة بـ
                            <strong>{{ $subscriber->company_name }}</strong>
                        </p>
                        <p class="text-muted mb-4">
                            يرجى التواصل مع مالك النظام لتجديد الاشتراك أو تحويل النسخة من تجريبية إلى مدفوعة.
                            لا يمكن إنشاء فواتير حقيقية أثناء فترة التوقف.
                        </p>
                        <div class="card text-start border-0 bg-light mb-3">
                            <div class="card-body">
                                <p class="mb-1"><strong>تاريخ الانتهاء:</strong> {{ optional($subscriber->subscription_end)->format('Y-m-d') ?? '-' }}</p>
                                @if($subscriber->is_trial)
                                    <p class="mb-0"><strong>نوع الاشتراك:</strong> نسخة تجريبية (30 يوماً)</p>
                                @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="btn btn-secondary" type="submit">تسجيل الخروج</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
