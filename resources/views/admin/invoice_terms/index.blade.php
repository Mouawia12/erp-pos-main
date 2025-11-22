@extends('admin.layouts.master')
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="alert alert-primary text-center w-100 mb-0">{{ __('main.invoice_terms') }}</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.invoice_terms.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>{{ __('main.name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-9">
                                <label>{{ __('main.invoice_terms') }}</label>
                                <textarea name="content" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="text-end mb-3">
                            <button type="submit" class="btn btn-primary">{{ __('main.save_btn') }}</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.name') }}</th>
                                    <th>{{ __('main.invoice_terms') }}</th>
                                    <th>{{ __('main.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $template->name }}</td>
                                        <td style="white-space: pre-wrap;">{{ $template->content }}</td>
                                        <td class="d-flex gap-2">
                                            <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editTerm{{ $template->id }}">
                                                {{ __('main.edit') }}
                                            </button>
                                            <form method="POST" action="{{ route('admin.invoice_terms.destroy', $template) }}" onsubmit="return confirm('{{ __('main.delete_confirm') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">{{ __('main.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editTerm{{ $template->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('main.edit') }} - {{ $template->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ route('admin.invoice_terms.update', $template) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="col-md-4 mb-2">
                                                                <label>{{ __('main.name') }}</label>
                                                                <input type="text" name="name" class="form-control" value="{{ $template->name }}" required>
                                                            </div>
                                                            <div class="col-md-8 mb-2">
                                                                <label>{{ __('main.invoice_terms') }}</label>
                                                                <textarea name="content" class="form-control" rows="4" required>{{ $template->content }}</textarea>
                                                            </div>
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
                                    <tr>
                                        <td colspan="4" class="text-center">{{ __('main.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
