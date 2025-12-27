@extends('admin.layouts.master')
@section('content')
@can('عرض تقارير')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.salon_reservations') ?? 'حجوزات المشغل' }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('salon.reservations.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('main.clients') }}</label>
                            <select name="customer_id" class="form-control" required>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.salon_department') ?? 'قسم المشغل' }}</label>
                            <select name="salon_department_id" class="form-control">
                                <option value="">{{ __('main.choose') }}</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.department_users') ?? 'مستخدمو القسم' }}</label>
                            <select name="assigned_user_id" class="form-control">
                                <option value="">{{ __('main.choose') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.reservation_time') ?? 'وقت الحجز' }}</label>
                            <input type="datetime-local" name="reservation_time" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.location_text') ?? 'وصف الموقع' }}</label>
                            <input type="text" name="location_text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.location_url') ?? 'رابط خرائط قوقل' }}</label>
                            <input type="text" name="location_url" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="scheduled">{{ __('main.status_scheduled') ?? 'مجدول' }}</option>
                                <option value="completed">{{ __('main.status_completed') ?? 'مكتمل' }}</option>
                                <option value="cancelled">{{ __('main.status_cancelled') ?? 'ملغي' }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('main.save_btn') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.salon_reservations_list') ?? 'قائمة الحجوزات' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.clients') }}</th>
                                    <th>{{ __('main.salon_department') ?? 'قسم المشغل' }}</th>
                                    <th>{{ __('main.department_users') ?? 'مستخدمو القسم' }}</th>
                                    <th>{{ __('main.reservation_time') ?? 'وقت الحجز' }}</th>
                                    <th>{{ __('main.location_url') ?? 'الموقع' }}</th>
                                    <th>{{ __('main.status') }}</th>
                                    <th>{{ __('main.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $idx => $reservation)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $reservation->customer?->name }}</td>
                                        <td>{{ $reservation->department?->name }}</td>
                                        <td>{{ $reservation->assignedUser?->name }}</td>
                                        <td>{{ $reservation->reservation_time }}</td>
                                        <td>
                                            @if($reservation->location_url)
                                                <a href="{{ $reservation->location_url }}" target="_blank">{{ __('main.open_map') ?? 'فتح الخريطة' }}</a>
                                            @else
                                                {{ $reservation->location_text }}
                                            @endif
                                        </td>
                                        <td>{{ $reservation->status }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" type="button" data-toggle="modal" data-target="#editReservation{{ $reservation->id }}">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <form method="POST" action="{{ route('salon.reservations.delete', $reservation->id) }}" style="display:inline-block" onsubmit="return confirm('{{ __('main.delete_alert') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editReservation{{ $reservation->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('main.salon_reservations') ?? 'حجوزات المشغل' }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ route('salon.reservations.update', $reservation->id) }}">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label>{{ __('main.clients') }}</label>
                                                            <select name="customer_id" class="form-control" required>
                                                                @foreach($customers as $customer)
                                                                    <option value="{{ $customer->id }}" @if($reservation->customer_id == $customer->id) selected @endif>{{ $customer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.salon_department') ?? 'قسم المشغل' }}</label>
                                                            <select name="salon_department_id" class="form-control">
                                                                <option value="">{{ __('main.choose') }}</option>
                                                                @foreach($departments as $department)
                                                                    <option value="{{ $department->id }}" @if($reservation->salon_department_id == $department->id) selected @endif>{{ $department->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.department_users') ?? 'مستخدمو القسم' }}</label>
                                                            <select name="assigned_user_id" class="form-control">
                                                                <option value="">{{ __('main.choose') }}</option>
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->id }}" @if($reservation->assigned_user_id == $user->id) selected @endif>{{ $user->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.reservation_time') ?? 'وقت الحجز' }}</label>
                                                            <input type="datetime-local" name="reservation_time" class="form-control" value="{{ $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('Y-m-d\\TH:i') : '' }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.location_text') ?? 'وصف الموقع' }}</label>
                                                            <input type="text" name="location_text" class="form-control" value="{{ $reservation->location_text }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.location_url') ?? 'رابط خرائط قوقل' }}</label>
                                                            <input type="text" name="location_url" class="form-control" value="{{ $reservation->location_url }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.status') }}</label>
                                                            <select name="status" class="form-control">
                                                                <option value="scheduled" @if($reservation->status === 'scheduled') selected @endif>{{ __('main.status_scheduled') ?? 'مجدول' }}</option>
                                                                <option value="completed" @if($reservation->status === 'completed') selected @endif>{{ __('main.status_completed') ?? 'مكتمل' }}</option>
                                                                <option value="cancelled" @if($reservation->status === 'cancelled') selected @endif>{{ __('main.status_cancelled') ?? 'ملغي' }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.notes') }}</label>
                                                            <textarea name="notes" class="form-control" rows="2">{{ $reservation->notes }}</textarea>
                                                        </div>
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-primary">{{ __('main.save_btn') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr><td colspan="8">{{ __('main.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
