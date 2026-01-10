@extends('admin.layouts.master')
@section('content')
@can('عرض تقارير')
<style>
    .reservation-barcode-wrap,
    .reservation-quick-add {
        position: relative;
    }
    .reservation-barcode-wrap .list-group,
    .reservation-quick-add .list-group {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        max-height: 220px;
        overflow-y: auto;
    }
</style>
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
                    <h5 class="mb-0">{{ __('main.salon_reservations') ?? 'قسم الحجز' }}</h5>
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
                            <label>{{ __('main.warehouse') ?? 'المستودع' }}</label>
                            <select name="warehouse_id" class="form-control" required>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
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
                        <div class="form-group">
                            <label>{{ __('main.items') ?? 'الأصناف' }}</label>
                            <div class="mb-2">
                                <div class="reservation-quick-add position-relative">
                                    <input type="text" class="form-control" id="reservation_add_item" placeholder="{{ __('main.add_item_hint') ?? 'أضف صنف (باركود أو اسم)' }}" autocomplete="off">
                                    <ul id="reservation_products_suggestions" class="suggestions" style="display: none;"></ul>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="reservationItemsTable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('main.barcode') ?? 'الباركود' }}</th>
                                            <th>{{ __('main.product') ?? 'الصنف' }}</th>
                                            <th>{{ __('main.quantity') ?? 'الكمية' }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="reservation-barcode-wrap">
                                                    <input type="text" name="item_barcode[]" class="form-control reservation-barcode" placeholder="{{ __('main.barcode') ?? 'الباركود' }}">
                                                    <div class="reservation-suggestions list-group d-none"></div>
                                                </div>
                                                <input type="hidden" name="item_product_id[]" class="reservation-product-id" required>
                                            </td>
                                            <td>
                                                <span class="reservation-product-name text-muted">--</span>
                                            </td>
                                            <td>
                                                <input type="number" step="0.0001" min="0.0001" name="item_qty[]" class="form-control" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-reservation-item" disabled>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addReservationItemRow">
                                {{ __('main.add_new') ?? 'إضافة' }}
                            </button>
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
                                    <th>{{ __('main.code') ?? 'الرقم' }}</th>
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
                                        <td>{{ $reservation->reservation_no ?? $reservation->id }}</td>
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
                                                    <h5 class="modal-title">{{ __('main.salon_reservations') ?? 'قسم الحجز' }}</h5>
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
                                                            <label>{{ __('main.warehouse') ?? 'المستودع' }}</label>
                            <select name="warehouse_id" id="reservation_warehouse_id" class="form-control" required>
                                                                @foreach($warehouses as $warehouse)
                                                                    <option value="{{ $warehouse->id }}" @if($reservation->warehouse_id == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
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
                                                                <option value="invoiced" @if($reservation->status === 'invoiced') selected @endif>{{ __('main.invoiced') ?? 'تمت الفوترة' }}</option>
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
@section('js')
<script>
    (function(){
        const table = document.getElementById('reservationItemsTable');
        const addBtn = document.getElementById('addReservationItemRow');
        const productLookupRoute = "{{ route('getProduct', ':code') }}";
        const quickInput = document.getElementById('reservation_add_item');
        const quickSuggestions = document.getElementById('reservation_products_suggestions');
        const warehouseSelect = document.getElementById('reservation_warehouse_id');
        if (!table || !addBtn) {
            return;
        }
        function applyProductToRow(product, row){
            const productIdInput = row.querySelector('.reservation-product-id');
            const nameLabel = row.querySelector('.reservation-product-name');
            const barcodeInput = row.querySelector('.reservation-barcode');
            if (productIdInput) {
                productIdInput.value = product.id || '';
            }
            if (nameLabel) {
                nameLabel.textContent = product.name || product.code || '--';
            }
            if (barcodeInput) {
                barcodeInput.value = product.code || product.barcode || '';
            }
        }

        function createRowWithProduct(product){
            const tbody = table.querySelector('tbody');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach(function(input){
                input.value = '';
            });
            const nameLabel = newRow.querySelector('.reservation-product-name');
            if (nameLabel) {
                nameLabel.textContent = '--';
            }
            const removeBtn = newRow.querySelector('.remove-reservation-item');
            if (removeBtn) {
                removeBtn.disabled = false;
            }
            tbody.appendChild(newRow);
            bindBarcodeInput(newRow.querySelector('.reservation-barcode'));
            applyProductToRow(product, newRow);
            const qtyInput = newRow.querySelector('input[name="item_qty[]"]');
            if (qtyInput) {
                qtyInput.value = qtyInput.value || '1';
                qtyInput.focus();
            }
        }

        function bindBarcodeInput(input){
            if (!input) {
                return;
            }
            const row = input.closest('tr');
            const productIdInput = row.querySelector('.reservation-product-id');
            const nameLabel = row.querySelector('.reservation-product-name');
            const suggestions = row.querySelector('.reservation-suggestions');

            function applyProduct(product){
                applyProductToRow(product, row);
                if (suggestions) {
                    suggestions.classList.add('d-none');
                    suggestions.innerHTML = '';
                }
            }

            input.addEventListener('input', function(){
                const raw = input.value.trim();
                if (!raw) {
                    if (suggestions) {
                        suggestions.classList.add('d-none');
                        suggestions.innerHTML = '';
                    }
                    if (productIdInput) {
                        productIdInput.value = '';
                    }
                    if (nameLabel) {
                        nameLabel.textContent = '--';
                    }
                    return;
                }
                // البحث بالأحرف مثل الفواتير: استخدم قيمة خالية لمطابقة الكل كفallback عند البحث بالاسم
                const searchValue = encodeURIComponent(raw);
                const url = productLookupRoute.replace(':code', searchValue);
                $.get(url, function(response){
                    if (!Array.isArray(response) || response.length === 0) {
                        if (suggestions) {
                            suggestions.classList.add('d-none');
                            suggestions.innerHTML = '';
                        }
                        if (nameLabel) {
                            nameLabel.textContent = 'غير موجود';
                        }
                        if (productIdInput) {
                            productIdInput.value = '';
                        }
                        return;
                    }
                    if (response.length === 1 && (response[0].code === raw || response[0].barcode === raw)) {
                        applyProduct(response[0]);
                        return;
                    }
                    if (suggestions) {
                        suggestions.innerHTML = '';
                        response.forEach(function(product){
                            const label = (product.name || product.code || '') + (product.code ? ' - ' + product.code : '');
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = label;
                            item.addEventListener('click', function(){
                                applyProduct(product);
                            });
                            suggestions.appendChild(item);
                        });
                        suggestions.classList.remove('d-none');
                    }
                });
            });

            document.addEventListener('click', function(event){
                if (!suggestions || suggestions.classList.contains('d-none')) {
                    return;
                }
                if (!row.contains(event.target)) {
                    suggestions.classList.add('d-none');
                }
            });
        }
        table.querySelectorAll('.reservation-barcode').forEach(bindBarcodeInput);

        let lastQuickAdded = '';
        let reservationSuggestionItems = {};

        function showReservationSuggestions(response){
            if (!quickSuggestions) {
                return;
            }
            let html = '';
            reservationSuggestionItems = {};
            response.forEach(function(item){
                reservationSuggestionItems[item.id] = item;
                const label = (item.name || item.code || '');
                html += '<li class="reservation-select-product" data-item-id="'+item.id+'">'+label+'</li>';
            });
            quickSuggestions.innerHTML = html;
            quickSuggestions.style.display = 'block';
        }

        function reservationSearchProduct(code){
            if (!warehouseSelect || !warehouseSelect.value) {
                return;
            }
            const warehouseId = warehouseSelect.value;
            let url = "{{ route('get.product.warehouse', [':warehouse', ':id']) }}";
            url = url.replace(':warehouse', warehouseId);
            url = url.replace(':id', encodeURIComponent(code));
            $.get(url, function(response){
                if (!Array.isArray(response) || response.length === 0) {
                    if (quickSuggestions) {
                        quickSuggestions.style.display = 'none';
                        quickSuggestions.innerHTML = '';
                    }
                    return;
                }
                if (response.length === 1) {
                    if (lastQuickAdded !== code) {
                        createRowWithProduct(response[0]);
                        lastQuickAdded = code;
                    }
                    if (quickInput) {
                        quickInput.value = '';
                    }
                    if (quickSuggestions) {
                        quickSuggestions.style.display = 'none';
                        quickSuggestions.innerHTML = '';
                    }
                    return;
                }
                lastQuickAdded = '';
                showReservationSuggestions(response);
            });
        }

        if (quickInput) {
            quickInput.addEventListener('input', function(){
                const code = quickInput.value.trim();
                if (!code) {
                    if (quickSuggestions) {
                        quickSuggestions.style.display = 'none';
                        quickSuggestions.innerHTML = '';
                    }
                    return;
                }
                reservationSearchProduct(code);
            });
        }

        addBtn.addEventListener('click', function(){
            const tbody = table.querySelector('tbody');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach(function(input){
                input.value = '';
            });
            const nameLabel = newRow.querySelector('.reservation-product-name');
            if (nameLabel) {
                nameLabel.textContent = '--';
            }
            const removeBtn = newRow.querySelector('.remove-reservation-item');
            if (removeBtn) {
                removeBtn.disabled = false;
            }
            tbody.appendChild(newRow);
            bindBarcodeInput(newRow.querySelector('.reservation-barcode'));
        });

        table.addEventListener('click', function(event){
            const target = event.target.closest('.remove-reservation-item');
            if (!target) {
                return;
            }
            const rows = table.querySelectorAll('tbody tr');
            if (rows.length <= 1) {
                return;
            }
            target.closest('tr').remove();
        });

        document.addEventListener('click', function(event){
            if (!quickSuggestions || quickSuggestions.style.display === 'none') {
                return;
            }
            if (!quickSuggestions.contains(event.target) && event.target !== quickInput) {
                quickSuggestions.style.display = 'none';
            }
        });

        document.addEventListener('click', function(event){
            const target = event.target.closest('.reservation-select-product');
            if (!target) {
                return;
            }
            const itemId = target.getAttribute('data-item-id');
            const product = reservationSuggestionItems[itemId];
            if (product) {
                createRowWithProduct(product);
            }
            if (quickInput) {
                quickInput.value = '';
                quickInput.focus();
            }
            if (quickSuggestions) {
                quickSuggestions.style.display = 'none';
                quickSuggestions.innerHTML = '';
            }
        });
    })();
</script>
@endsection
@endsection
