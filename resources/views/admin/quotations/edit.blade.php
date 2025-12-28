@extends('admin.layouts.master')
@section('content')
@php
    $userBranchId = Auth::user()->branch_id ?? null;
    $selectedBranch = old('branch_id', $quotation->branch_id ?? $userBranchId);
    $defaultCustomerId = old('customer_id', $quotation->customer_id ?? optional($walkInCustomer)->id ?? optional($customers->first())->id);
    $showWalkInFields = $walkInCustomer && $defaultCustomerId == $walkInCustomer->id;
@endphp
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.edit') }} - {{ $quotation->quotation_no }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">{{ __('main.back') ?? 'عودة' }}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('quotations.update',$quotation) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>{{ __('main.invoice_no') }}</label>
                        <input type="text" class="form-control" readonly value="{{ $quotation->quotation_no }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.bill_date') }}</label>
                        <input type="datetime-local" class="form-control" value="{{ optional($quotation->date)->format('Y-m-d\TH:i') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.invoice_type') }}</label>
                        <select class="form-control" name="invoice_type" id="invoice_type">
                            <option value="tax_invoice" @if(($quotation->invoice_type ?? $defaultInvoiceType ?? 'tax_invoice')==='tax_invoice') selected @endif>{{ __('main.invoice_type_tax') }}</option>
                            <option value="simplified_tax_invoice" @if(($quotation->invoice_type ?? $defaultInvoiceType ?? '')==='simplified_tax_invoice') selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                            <option value="non_tax_invoice" @if(($quotation->invoice_type ?? '')==='non_tax_invoice') selected @endif>{{ __('main.invoice_type_nontax') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.branche') }}</label>
                        @if(empty($userBranchId))
                            <select name="branch_id" id="branch_id" class="form-control">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @if($selectedBranch==$branch->id) selected @endif>{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" readonly value="{{ Auth::user()->branch->branch_name ?? '' }}">
                            <input type="hidden" name="branch_id" value="{{ $selectedBranch }}">
                        @endif
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-3">
                        <label>{{ __('main.warehouse') }}</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @if(old('warehouse_id', $quotation->warehouse_id)==$warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.clients') }}</label>
                        <select name="customer_id" id="customer_id" class="form-control" required data-walk-in="{{ optional($walkInCustomer)->id }}">
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                        data-phone="{{ $customer->phone }}"
                                        data-address="{{ $customer->address }}"
                                        data-tax="{{ $customer->tax_number }}"
                                        data-name="{{ $customer->name }}"
                                        @if($defaultCustomerId == $customer->id) selected @endif>
                                    {{ $customer->name }}
                                    @if($walkInCustomer && $walkInCustomer->id === $customer->id)
                                        ({{ __('main.walk_in_customer') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="walk_in_customer_id" value="{{ optional($walkInCustomer)->id }}">
                    </div>
                    <div class="col-md-3 {{ $showWalkInFields ? '' : 'd-none' }}" id="walk_in_fields">
                        <label>{{ __('main.customer_name') }}</label>
                        <input type="text" class="form-control" name="customer_name" id="customer_name_input" value="{{ old('customer_name', $quotation->customer_name) }}" placeholder="{{ __('main.customer_name') }}">
                        <div class="mt-2">
                            <label class="mb-0">{{ __('main.customer_phone') }}</label>
                            <input type="text" class="form-control" name="customer_phone" id="customer_phone_input" value="{{ old('customer_phone', $quotation->customer_phone) }}" placeholder="{{ __('main.customer_phone') }}">
                            <small class="text-muted">{{ __('main.walk_in_customer_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3" id="customer_meta_fields">
                        <label>{{ __('main.customer_phone') }}</label>
                        <input type="text" class="form-control" id="customer_phone_display" value="{{ $quotation->customer_phone }}" placeholder="{{ __('main.customer_phone') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.address') }}</label>
                        <input type="text" class="form-control" name="customer_address" id="customer_address" value="{{ old('customer_address', $quotation->customer_address) }}" placeholder="{{ __('main.address') }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.tax_number') }}</label>
                        <input type="text" class="form-control" name="customer_tax_number" id="customer_tax_number" value="{{ old('customer_tax_number', $quotation->customer_tax_number) }}" placeholder="{{ __('main.tax_number') }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.representatives') }}</label>
                        <select class="form-control" name="representative_id" id="representative_id">
                            <option value="">{{ __('main.choose') }}</option>
                            @foreach($representatives as $rep)
                                <option value="{{ $rep->id }}" @if(old('representative_id', $quotation->representative_id)==$rep->id) selected @endif>{{ $rep->user_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.cost_center') }}</label>
                        <select class="form-control mb-1" name="cost_center_id" id="cost_center_id">
                            <option value="">{{ __('main.choose') }}</option>
                            @foreach($costCenters as $center)
                                <option value="{{$center->id}}" @if(old('cost_center_id', $quotation->cost_center_id)==$center->id) selected @endif>{{$center->name}}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control" name="cost_center" id="cost_center" value="{{ old('cost_center', $quotation->cost_center) }}" placeholder="{{ __('main.cost_center') }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.invoice_payment_method') ?? __('main.payment_method') }}</label>
                        <select class="form-control" name="payment_method" id="payment_method">
                            <option value="cash" @if(old('payment_method', $quotation->payment_method ?? 'cash')==='cash') selected @endif>{{ __('main.cash') }}</option>
                            <option value="credit" @if(old('payment_method', $quotation->payment_method ?? 'cash')==='credit') selected @endif>{{ __('main.credit') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.note') ?? 'ملاحظات' }}</label>
                        <input type="text" name="note" class="form-control" value="{{ old('note', $quotation->note) }}">
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                        <tr>
                            <th>{{ __('main.item') }}</th>
                            <th>{{ __('main.quantity') }}</th>
                            <th>{{ __('main.price') }}</th>
                            <th>{{ __('main.tax') }}</th>
                            <th>{{ __('main.total') }}</th>
                            <th>{{ __('main.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">+ {{ __('main.add_new') }}</button>
                <hr>
                <div class="text-end">
                    <button class="btn btn-success" type="submit">{{ __('main.save_btn') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="duplicateItemModal" tabindex="-1" role="dialog" aria-labelledby="duplicateItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="alertTitle mb-0">{{ __('main.alerts') }}</label>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">{{ __('main.duplicate_item_warning') }}</p>
                <p class="text-muted" id="duplicateItemName"></p>
                <div class="d-flex justify-content-around mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelDuplicateItem">{{ __('main.cancel_btn') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmDuplicateItem">{{ __('main.add_new') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const products = @json($products);
    const productsMap = {};
    products.forEach(p => productsMap[p.id] = p);
    const existing = @json($quotation->details);
    let pendingDuplicateSelect = null;
    let pendingDuplicateProduct = null;

    function addRow(data = {}) {
        const tbody = document.querySelector('#itemsTable tbody');
        const index = tbody.children.length;
        const row = document.createElement('tr');

        let options = '<option value="">--</option>';
        products.forEach(p=>{
            options += `<option value="${p.id}" data-price="${p.price}" data-variants='${JSON.stringify(p.variants ?? [])}' ${data.product_id==p.id?'selected':''}>${p.name}</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="product_id[]" class="form-control productSelect" data-index="${index}">
                    ${options}
                </select>
                <input type="hidden" name="variant_id[]" class="variantId" value="${data.variant_id ?? ''}">
                <div class="small text-muted variant-label">${data.variant_color ?? ''} ${data.variant_size ?? ''}</div>
            </td>
            <td><input type="number" step="0.01" name="qnt[]" class="form-control qnt" value="${data.quantity ?? 1}"></td>
            <td><input type="number" step="0.01" name="price[]" class="form-control price" value="${data.price_unit ?? 0}"></td>
            <td><input type="number" step="0.01" name="tax[]" class="form-control tax" value="${data.tax ?? 0}"></td>
            <td><input type="number" step="0.01" class="form-control total" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
        `;
        tbody.appendChild(row);
        recalcRow(row);
    }

    function recalcRow(row){
        const qty = parseFloat(row.querySelector('.qnt').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;
        const total = (qty * price) + tax;
        row.querySelector('.total').value = total.toFixed(2);
    }

    document.getElementById('addRowBtn').addEventListener('click', ()=>addRow());

    document.addEventListener('input', function(e){
        if(e.target.classList.contains('qnt') || e.target.classList.contains('price') || e.target.classList.contains('tax')){
            recalcRow(e.target.closest('tr'));
        }
    });

    document.addEventListener('click', function(e){
        if(e.target.classList.contains('removeRow')){
            e.target.closest('tr').remove();
        }
    });

    document.addEventListener('change', function(e){
        if(e.target.classList.contains('productSelect')){
            const variants = JSON.parse(e.target.selectedOptions[0].dataset.variants || '[]');
            let label = '';
            let variantId = '';
            if(variants.length === 1){
                variantId = variants[0].id;
                label = (variants[0].color ?? '')+' '+(variants[0].size ?? '');
                const priceField = e.target.closest('tr').querySelector('.price');
                if(variants[0].price){
                    priceField.value = variants[0].price;
                }
                const variantInput = e.target.closest('tr').querySelector('.variantId');
                variantInput.value = variantId;
            }
            e.target.closest('td').querySelector('.variant-label').innerText = label;
            recalcRow(e.target.closest('tr'));
        }
    });

        row.innerHTML = `
            <td>
                <select name="product_id[]" class="form-control productSelect" data-index="${index}">
                    ${options}
                </select>
                <input type="hidden" name="variant_id[]" class="variantId" value="${data.variant_id ?? ''}">
                <div class="small text-muted variant-label">${data.variant_color ?? ''} ${data.variant_size ?? ''}</div>
            </td>
            <td><input type="number" step="0.01" name="qnt[]" class="form-control qnt" value="${data.quantity ?? 1}"></td>
            <td><input type="number" step="0.01" name="price[]" class="form-control price" value="${data.price_unit ?? 0}"></td>
            <td><input type="number" step="0.01" name="tax[]" class="form-control tax" value="${data.tax ?? 0}"></td>
            <td><input type="number" step="0.01" class="form-control total" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
        `;
        tbody.appendChild(row);
        recalcRow(row);
    }

    function recalcRow(row){
        const qty = parseFloat(row.querySelector('.qnt').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;
        const total = (qty * price) + tax;
        row.querySelector('.total').value = total.toFixed(2);
    }

    function isDuplicateProduct(productId, currentSelect){
        if(!productId){
            return false;
        }
        const selects = document.querySelectorAll('.productSelect');
        let occurrences = 0;
        selects.forEach(select => {
            if(select.value === productId){
                if(!currentSelect || select !== currentSelect){
                    occurrences++;
                }
            }
        });
        return occurrences > 0;
    }

    function applyProductSelection(selectElement){
        const productId = selectElement.value;
        const product = productsMap[productId];
        if(!product){
            selectElement.closest('td').querySelector('.variant-label').innerText = '';
            selectElement.closest('td').querySelector('.variantId').value = '';
            return;
        }

        const variants = product.variants || [];
        let label = '';
        let variantId = '';
        if(variants.length === 1){
            variantId = variants[0].id;
            label = (variants[0].color ?? '')+' '+(variants[0].size ?? '');
            const priceField = selectElement.closest('tr').querySelector('.price');
            if(variants[0].price){
                priceField.value = variants[0].price;
            }
        }
        selectElement.closest('td').querySelector('.variant-label').innerText = label;
        selectElement.closest('td').querySelector('.variantId').value = variantId;
        recalcRow(selectElement.closest('tr'));
    }

    document.getElementById('addRowBtn').addEventListener('click', ()=>addRow());

    document.addEventListener('input', function(e){
        if(e.target.classList.contains('qnt') || e.target.classList.contains('price') || e.target.classList.contains('tax')){
            recalcRow(e.target.closest('tr'));
        }
    });

    document.addEventListener('click', function(e){
        if(e.target.classList.contains('removeRow')){
            e.target.closest('tr').remove();
        }
    });

    document.addEventListener('focus', function(e){
        if(e.target.classList.contains('productSelect')){
            e.target.dataset.previousValue = e.target.value || '';
        }
    }, true);

    document.addEventListener('change', function(e){
        if(e.target.classList.contains('productSelect')){
            const selectedProductId = e.target.value;
            if(selectedProductId && isDuplicateProduct(selectedProductId, e.target)){
                pendingDuplicateSelect = e.target;
                pendingDuplicateProduct = productsMap[selectedProductId];
                document.getElementById('duplicateItemName').innerText = pendingDuplicateProduct ? pendingDuplicateProduct.name : '';
                $('#duplicateItemModal').modal({backdrop:'static', keyboard:false});
                return;
            }
            applyProductSelection(e.target);
            e.target.dataset.previousValue = e.target.value;
        }
    });

    document.getElementById('cancelDuplicateItem').addEventListener('click', function(){
        if(pendingDuplicateSelect){
            pendingDuplicateSelect.value = pendingDuplicateSelect.dataset.previousValue || '';
            applyProductSelection(pendingDuplicateSelect);
        }
        pendingDuplicateSelect = null;
        pendingDuplicateProduct = null;
        $('#duplicateItemModal').modal('hide');
    });

    document.getElementById('confirmDuplicateItem').addEventListener('click', function(){
        if(pendingDuplicateSelect){
            applyProductSelection(pendingDuplicateSelect);
            pendingDuplicateSelect.dataset.previousValue = pendingDuplicateSelect.value;
        }
        pendingDuplicateSelect = null;
        pendingDuplicateProduct = null;
        $('#duplicateItemModal').modal('hide');
    });

    if(existing && existing.length){
        existing.forEach(d=>addRow(d));
    } else {
        addRow();
    }

    const customerSelect = document.getElementById('customer_id');
    const walkInFields = document.getElementById('walk_in_fields');
    const staticPhoneDisplay = document.getElementById('customer_phone_display');
    const walkInPhoneInput = document.getElementById('customer_phone_input');
    const customerNameInput = document.getElementById('customer_name_input');
    const addressInput = document.getElementById('customer_address');
    const taxInput = document.getElementById('customer_tax_number');
    const walkInId = parseInt(document.getElementById('walk_in_customer_id').value || 0);

    function toggleCustomerFields(){
        const selected = customerSelect.options[customerSelect.selectedIndex];
        const isWalkIn = walkInId && Number(selected.value) === walkInId;
        if(isWalkIn){
            walkInFields.classList.remove('d-none');
            staticPhoneDisplay.closest('.col-md-3').classList.add('d-none');
        }else{
            walkInFields.classList.add('d-none');
            staticPhoneDisplay.closest('.col-md-3').classList.remove('d-none');
        }

        const phone = selected.dataset.phone || '';
        const address = selected.dataset.address || '';
        const tax = selected.dataset.tax || '';
        const name = selected.dataset.name || selected.text;

        if(!isWalkIn){
            customerNameInput.value = name;
            walkInPhoneInput.value = phone;
            staticPhoneDisplay.value = phone;
        }else{
            staticPhoneDisplay.value = '';
        }
        addressInput.value = address;
        taxInput.value = tax;
    }

    if(customerSelect){
        customerSelect.addEventListener('change', toggleCustomerFields);
        toggleCustomerFields();
    }

    const costCenterSelect = document.getElementById('cost_center_id');
    const costCenterInput = document.getElementById('cost_center');
    if(costCenterSelect){
        costCenterSelect.addEventListener('change', function(){
            const selected = costCenterSelect.options[costCenterSelect.selectedIndex];
            if(selected && selected.value && costCenterInput && !costCenterInput.value){
                costCenterInput.value = selected.text;
            }
        });
    }
</script>
@endsection
