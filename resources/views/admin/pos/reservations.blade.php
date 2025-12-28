@extends('admin.layouts.master')
@section('content')
@can('عرض مبيعات')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.pos_reservations') ?? 'حجوزات نقاط البيع' }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pos.reservations.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('main.customer_name') }}</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.customer_phone') }}</label>
                            <input type="text" name="customer_phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.reservation_time') }}</label>
                            <input type="datetime-local" name="reservation_time" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.guests_count') }}</label>
                            <input type="number" min="1" name="guests" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.session_location') }}</label>
                            <input type="text" name="session_location" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.section') ?? 'القسم' }}</label>
                            <select name="pos_section_id" class="form-control">
                                <option value="">{{ __('main.choose') }}</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="booked">{{ __('main.booked') ?? 'محجوز' }}</option>
                                <option value="seated">{{ __('main.seated') ?? 'تم التسكين' }}</option>
                                <option value="cancelled">{{ __('main.cancelled') ?? 'ملغي' }}</option>
                                <option value="no_show">{{ __('main.no_show') ?? 'لم يحضر' }}</option>
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
            <div class="card mb-3">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.filters') ?? 'الفلاتر' }}</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('pos.reservations') }}" class="row">
                        <div class="col-md-4">
                            <label>{{ __('main.from_date') ?? 'من تاريخ' }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('main.to_date') ?? 'إلى تاريخ' }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('main.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="">{{ __('main.all') }}</option>
                                <option value="booked" @if(request('status') === 'booked') selected @endif>{{ __('main.booked') ?? 'محجوز' }}</option>
                                <option value="seated" @if(request('status') === 'seated') selected @endif>{{ __('main.seated') ?? 'تم التسكين' }}</option>
                                <option value="cancelled" @if(request('status') === 'cancelled') selected @endif>{{ __('main.cancelled') ?? 'ملغي' }}</option>
                                <option value="no_show" @if(request('status') === 'no_show') selected @endif>{{ __('main.no_show') ?? 'لم يحضر' }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('main.section') ?? 'القسم' }}</label>
                            <select name="section_id" class="form-control">
                                <option value="">{{ __('main.all') }}</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" @if((int) request('section_id') === (int) $section->id) selected @endif>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">{{ __('main.search') ?? 'بحث' }}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.reservations_list') ?? 'قائمة الحجوزات' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.customer_name') }}</th>
                                    <th>{{ __('main.reservation_time') }}</th>
                                    <th>{{ __('main.guests_count') }}</th>
                                    <th>{{ __('main.section') ?? 'القسم' }}</th>
                                    <th>{{ __('main.status') }}</th>
                                    <th>{{ __('main.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $idx => $reservation)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $reservation->customer_name }}</td>
                                        <td>{{ $reservation->reservation_time }}</td>
                                        <td>{{ $reservation->guests ?? '--' }}</td>
                                        <td>{{ optional($sections->firstWhere('id', $reservation->pos_section_id))->name ?? '--' }}</td>
                                        <td>{{ $reservation->status }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-success" href="{{ route('pos', ['reservation_id' => $reservation->id]) }}" target="_blank">
                                                <i class="fa fa-cash-register"></i>
                                            </a>
                                            <button class="btn btn-sm btn-info" type="button" data-toggle="modal" data-target="#editReservation{{ $reservation->id }}">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <form method="POST" action="{{ route('pos.reservations.delete', $reservation) }}" style="display:inline-block" onsubmit="return confirm('{{ __('main.delete_alert') }}');">
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
                                                    <h5 class="modal-title">{{ __('main.pos_reservations') ?? 'حجوزات نقاط البيع' }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ route('pos.reservations.update', $reservation) }}">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label>{{ __('main.customer_name') }}</label>
                                                            <input type="text" name="customer_name" class="form-control" value="{{ $reservation->customer_name }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.customer_phone') }}</label>
                                                            <input type="text" name="customer_phone" class="form-control" value="{{ $reservation->customer_phone }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.reservation_time') }}</label>
                                                            <input type="datetime-local" name="reservation_time" class="form-control" value="{{ $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('Y-m-d\\TH:i') : '' }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.guests_count') }}</label>
                                                            <input type="number" min="1" name="guests" class="form-control" value="{{ $reservation->guests }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.session_location') }}</label>
                                                            <input type="text" name="session_location" class="form-control" value="{{ $reservation->session_location }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.section') ?? 'القسم' }}</label>
                                                            <select name="pos_section_id" class="form-control">
                                                                <option value="">{{ __('main.choose') }}</option>
                                                                @foreach($sections as $section)
                                                                    <option value="{{ $section->id }}" @if($reservation->pos_section_id == $section->id) selected @endif>{{ $section->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.status') }}</label>
                                                            <select name="status" class="form-control">
                                                                <option value="booked" @if($reservation->status === 'booked') selected @endif>{{ __('main.booked') ?? 'محجوز' }}</option>
                                                                <option value="seated" @if($reservation->status === 'seated') selected @endif>{{ __('main.seated') ?? 'تم التسكين' }}</option>
                                                                <option value="cancelled" @if($reservation->status === 'cancelled') selected @endif>{{ __('main.cancelled') ?? 'ملغي' }}</option>
                                                                <option value="no_show" @if($reservation->status === 'no_show') selected @endif>{{ __('main.no_show') ?? 'لم يحضر' }}</option>
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
                                    <tr><td colspan="7">{{ __('main.no_data') }}</td></tr>
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
