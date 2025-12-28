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
                    <h5 class="mb-0">{{ __('main.pos_sections') ?? 'أقسام نقاط البيع' }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pos.sections.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('main.name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.section_type') ?? 'النوع' }}</label>
                            <input type="text" name="type" class="form-control" placeholder="{{ __('main.section_type') ?? 'مطعم / كوفي' }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('main.status') }}</label>
                            <select name="is_active" class="form-control">
                                <option value="1">{{ __('main.status1') }}</option>
                                <option value="0">{{ __('main.status2') }}</option>
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
                    <h5 class="mb-0">{{ __('main.pos_sections_list') ?? 'قائمة أقسام نقاط البيع' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.name') }}</th>
                                    <th>{{ __('main.section_type') ?? 'النوع' }}</th>
                                    <th>{{ __('main.status') }}</th>
                                    <th>{{ __('main.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sections as $idx => $section)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $section->name }}</td>
                                        <td>{{ $section->type ?? '--' }}</td>
                                        <td>{{ $section->is_active ? __('main.status1') : __('main.status2') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" type="button" data-toggle="modal" data-target="#editSection{{ $section->id }}">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <form method="POST" action="{{ route('pos.sections.delete', $section) }}" style="display:inline-block" onsubmit="return confirm('{{ __('main.delete_alert') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editSection{{ $section->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('main.pos_sections') ?? 'أقسام نقاط البيع' }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ route('pos.sections.update', $section) }}">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label>{{ __('main.name') }}</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $section->name }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.section_type') ?? 'النوع' }}</label>
                                                            <input type="text" name="type" class="form-control" value="{{ $section->type }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.status') }}</label>
                                                            <select name="is_active" class="form-control">
                                                                <option value="1" @if($section->is_active) selected @endif>{{ __('main.status1') }}</option>
                                                                <option value="0" @if(!$section->is_active) selected @endif>{{ __('main.status2') }}</option>
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
                                    <tr><td colspan="5">{{ __('main.no_data') }}</td></tr>
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
