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
                    <h5 class="mb-0">{{ __('main.salon_departments') ?? 'أقسام المشغل' }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('salon.departments.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('main.name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.description') }}</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="1">{{ __('main.status1') }}</option>
                                <option value="0">{{ __('main.status2') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.department_users') ?? 'مستخدمو القسم' }}</label>
                            <select name="users[]" class="form-control" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('main.save_btn') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.salon_departments_list') ?? 'قائمة أقسام المشغل' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.name') }}</th>
                                    <th>{{ __('main.description') }}</th>
                                    <th>{{ __('main.department_users') ?? 'مستخدمو القسم' }}</th>
                                    <th>{{ __('main.status') }}</th>
                                    <th>{{ __('main.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $idx => $department)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $department->name }}</td>
                                        <td>{{ $department->description }}</td>
                                        <td>
                                            {{ $department->users->pluck('name')->implode(', ') }}
                                        </td>
                                        <td>{{ $department->status ? __('main.status1') : __('main.status2') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" type="button" data-toggle="modal" data-target="#editDept{{ $department->id }}">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <form method="POST" action="{{ route('salon.departments.delete', $department->id) }}" style="display:inline-block" onsubmit="return confirm('{{ __('main.delete_alert') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editDept{{ $department->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('main.salon_departments') ?? 'أقسام المشغل' }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ route('salon.departments.update', $department->id) }}">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label>{{ __('main.name') }}</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.description') }}</label>
                                                            <textarea name="description" class="form-control" rows="2">{{ $department->description }}</textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.status') }}</label>
                                                            <select name="status" class="form-control">
                                                                <option value="1" @if($department->status) selected @endif>{{ __('main.status1') }}</option>
                                                                <option value="0" @if(!$department->status) selected @endif>{{ __('main.status2') }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.department_users') ?? 'مستخدمو القسم' }}</label>
                                                            <select name="users[]" class="form-control" multiple>
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->id }}" @if($department->users->pluck('id')->contains($user->id)) selected @endif>{{ $user->name }}</option>
                                                                @endforeach
                                                            </select>
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
                                    <tr><td colspan="6">{{ __('main.no_data') }}</td></tr>
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
