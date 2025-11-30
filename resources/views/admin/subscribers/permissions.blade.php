@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 text-center">تعديل صلاحيات المشترك - {{ $subscriber->company_name }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('owner.subscribers.permissions.update', $subscriber) }}">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('main.max_branches') }}</label>
                                <input type="number" min="0" name="max_branches" class="form-control"
                                       value="{{ old('max_branches', $settings->max_branches) }}"
                                       placeholder="ضع 0 لغير محدود">
                                <small class="text-muted">هذا الحد يحدد أقصى عدد من الفروع التي يمكن لهذا المشترك إضافتها.</small>
                            </div>
                            <div class="text-center mt-4">
                                <a href="{{ route('owner.subscribers.index') }}" class="btn btn-secondary">رجوع</a>
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
