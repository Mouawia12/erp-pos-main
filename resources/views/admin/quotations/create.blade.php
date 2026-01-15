@extends('admin.layouts.master')
@section('content')
@php
    $userBranchId = Auth::user()->branch_id ?? null;
    $selectedBranch = old('branch_id', $userBranchId);
    $defaultCustomerId = old('customer_id', optional($walkInCustomer)->id ?? optional($customers->first())->id);
    $showWalkInFields = $walkInCustomer && $defaultCustomerId == $walkInCustomer->id;
@endphp
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.add') }} {{ __('main.quotation') }}</h4>
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
            <form method="POST" action="{{ route('quotations.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>{{ __('main.invoice_no') }}</label>
                        <input type="text" class="form-control" readonly value="{{ $nextQuotationNo }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.bill_date') }}</label>
                        <input type="datetime-local" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.invoice_type') }}</label>
                        <select class="form-control" name="invoice_type" id="invoice_type">
                            <option value="tax_invoice" @if(($defaultInvoiceType ?? 'tax_invoice')==='tax_invoice') selected @endif>{{ __('main.invoice_type_tax') }}</option>
                            <option value="simplified_tax_invoice" @if(($defaultInvoiceType ?? '')==='simplified_tax_invoice') selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                            <option value="non_tax_invoice" @if(($defaultInvoiceType ?? '')==='non_tax_invoice') selected @endif>{{ __('main.invoice_type_nontax') }}</option>
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
                            <input type="hidden" name="branch_id" value="{{ $userBranchId }}">
                        @endif
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-3">
                        <label>{{ __('main.warehouse') }}</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @if(old('warehouse_id')==$warehouse->id) selected @endif>{{ $warehouse->name }}</option>
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
                        <input type="text" class="form-control" name="customer_name" id="customer_name_input" value="{{ old('customer_name', optional($walkInCustomer)->name) }}" placeholder="{{ __('main.customer_name') }}">
                        <div class="mt-2">
                            <label class="mb-0">{{ __('main.customer_phone') }}</label>
                            <input type="text" class="form-control" name="customer_phone" id="customer_phone_input" value="{{ old('customer_phone', optional($walkInCustomer)->phone) }}" placeholder="{{ __('main.customer_phone') }}">
                            <small class="text-muted">{{ __('main.walk_in_customer_hint') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3" id="customer_meta_fields">
                        <label>{{ __('main.customer_phone') }}</label>
                        <input type="text" class="form-control" id="customer_phone_display" value="{{ old('customer_phone') }}" placeholder="{{ __('main.customer_phone') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.address') }}</label>
                        <input type="text" class="form-control" name="customer_address" id="customer_address" value="{{ old('customer_address') }}" placeholder="{{ __('main.address') }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.tax_number') }}</label>
                        <input type="text" class="form-control" name="customer_tax_number" id="customer_tax_number" value="{{ old('customer_tax_number') }}" placeholder="{{ __('main.tax_number') }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.representatives') }}</label>
                        <select class="form-control" name="representative_id" id="representative_id">
                            <option value="">{{ __('main.choose') }}</option>
                            @foreach($representatives as $rep)
                                <option value="{{ $rep->id }}" @if(old('representative_id')==$rep->id) selected @endif>{{ $rep->user_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.cost_center') }}</label>
                        <select class="form-control mb-1" name="cost_center_id" id="cost_center_id">
                            <option value="">{{ __('main.choose') }}</option>
                            @foreach($costCenters as $center)
                                <option value="{{$center->id}}">{{$center->name}}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control" name="cost_center" id="cost_center" value="{{ old('cost_center') }}" placeholder="{{ __('main.cost_center') }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.invoice_payment_method') ?? __('main.payment_method') }}</label>
                        <select class="form-control" name="payment_method" id="payment_method">
                            <option value="cash" @if(old('payment_method','cash')==='cash') selected @endif>{{ __('main.cash') }}</option>
                            <option value="credit" @if(old('payment_method')==='credit') selected @endif>{{ __('main.credit') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.note') ?? 'ملاحظات' }}</label>
                        <input type="text" name="note" class="form-control" value="{{ old('note') }}">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="well well-sm">
                            <div class="form-group">
                                <div class="input-group wide-tip">
                                    <div class="input-group-addon">
                                        <i class="fa fa-3x fa-barcode addIcon"></i>
                                    </div>
                                    <input type="text" id="quotation_add_item" class="form-control input-lg ui-autocomplete-input"
                                        placeholder="{{ __('main.barcode.note') }}"
                                        autocomplete="off">
                                </div>
                            </div>
                            <ul class="suggestions" id="quotation_products_suggestions" style="display: none;"></ul>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                        <tr>
                            <th>{{ __('main.barcode') ?? 'الباركود' }}</th>
                            <th>{{ __('main.product') ?? __('main.item') }}</th>
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
    const productLookupRoute = "{{ route('get.product.warehouse', [':warehouse', ':id']) }}";
    const itemsTableBody = document.querySelector('#itemsTable tbody');
    const addRowBtn = document.getElementById('addRowBtn');
    const quickInput = document.getElementById('quotation_add_item');
    const quickSuggestions = document.getElementById('quotation_products_suggestions');
    const warehouseSelect = document.getElementById('warehouse_id');

    let pendingDuplicateAction = null;

    function ensureWarehouseSelected(){
        if(!warehouseSelect || !warehouseSelect.value){
            alert('{{ __('main.customer_warehouse_required') }}');
            return false;
        }
        return true;
    }

    function buildLookupUrl(query){
        const warehouseId = warehouseSelect ? warehouseSelect.value : '';
        return productLookupRoute
            .replace(':warehouse', warehouseId || 0)
            .replace(':id', encodeURIComponent(query));
    }

    function fetchProducts(query, onSuccess){
        if(!ensureWarehouseSelected()){
            return;
        }
        const url = buildLookupUrl(query);
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function(response){
                onSuccess(Array.isArray(response) ? response : []);
            },
            error: function(){
                onSuccess([]);
            }
        });
    }

    function recalcRow(row){
        const qty = parseFloat(row.querySelector('.qnt').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;
        const total = (qty * price) + tax;
        row.querySelector('.total').value = total.toFixed(2);
    }

    function getRowState(row){
        return {
            productId: row.querySelector('.quotation-product-id').value,
            productName: row.querySelector('.quotation-product-name').textContent,
            barcode: row.querySelector('.quotation-barcode').value,
            price: row.querySelector('.price').value,
            tax: row.querySelector('.tax').value,
            variantId: row.querySelector('.quotation-variant-id').value,
            variantColor: row.querySelector('.quotation-variant-color').value,
            variantSize: row.querySelector('.quotation-variant-size').value,
            variantBarcode: row.querySelector('.quotation-variant-barcode').value,
            variantLabel: row.querySelector('.quotation-variant-label').textContent
        };
    }

    function restoreRowState(row, state){
        if(!state){
            clearRowProduct(row);
            return;
        }
        row.querySelector('.quotation-product-id').value = state.productId || '';
        row.querySelector('.quotation-product-name').textContent = state.productName || '--';
        row.querySelector('.quotation-barcode').value = state.barcode || '';
        row.querySelector('.price').value = state.price || 0;
        row.querySelector('.tax').value = state.tax || 0;
        row.querySelector('.quotation-variant-id').value = state.variantId || '';
        row.querySelector('.quotation-variant-color').value = state.variantColor || '';
        row.querySelector('.quotation-variant-size').value = state.variantSize || '';
        row.querySelector('.quotation-variant-barcode').value = state.variantBarcode || '';
        row.querySelector('.quotation-variant-label').textContent = state.variantLabel || '';
        recalcRow(row);
    }

    function clearRowProduct(row, keepBarcode){
        row.querySelector('.quotation-product-id').value = '';
        row.querySelector('.quotation-product-name').textContent = '--';
        if(!keepBarcode){
            row.querySelector('.quotation-barcode').value = '';
        }
        row.querySelector('.quotation-variant-id').value = '';
        row.querySelector('.quotation-variant-color').value = '';
        row.querySelector('.quotation-variant-size').value = '';
        row.querySelector('.quotation-variant-barcode').value = '';
        row.querySelector('.quotation-variant-label').textContent = '';
        row.querySelector('.price').value = 0;
        row.querySelector('.tax').value = 0;
        recalcRow(row);
    }

    function isDuplicateProduct(productId, currentRow){
        if(!productId){
            return false;
        }
        const rows = document.querySelectorAll('.quotation-product-id');
        let occurrences = 0;
        rows.forEach(input => {
            if(input.value === String(productId)){
                if(!currentRow || input !== currentRow.querySelector('.quotation-product-id')){
                    occurrences++;
                }
            }
        });
        return occurrences > 0;
    }

    function openDuplicateModal(product, onConfirm, onCancel){
        pendingDuplicateAction = { onConfirm, onCancel };
        document.getElementById('duplicateItemName').innerText = product && product.name ? product.name : '';
        $('#duplicateItemModal').modal({backdrop:'static', keyboard:false});
    }

    function applyProductToRow(product, row, forceDuplicate){
        if(!product || !row){
            return;
        }
        const previousState = getRowState(row);
        if(!forceDuplicate && isDuplicateProduct(product.id, row)){
            openDuplicateModal(product, function(){
                applyProductToRow(product, row, true);
            }, function(){
                restoreRowState(row, previousState);
            });
            return;
        }

        const barcodeInput = row.querySelector('.quotation-barcode');
        const productName = row.querySelector('.quotation-product-name');
        const productIdInput = row.querySelector('.quotation-product-id');
        const priceInput = row.querySelector('.price');
        const taxInput = row.querySelector('.tax');
        const variantLabel = row.querySelector('.quotation-variant-label');

        productIdInput.value = product.id || '';
        if(barcodeInput){
            barcodeInput.value = product.code || product.barcode || '';
        }
        if(productName){
            productName.textContent = product.name || product.code || '--';
        }

        const variants = product.variants || [];
        let variantId = '';
        let variantLabelText = '';
        let variantPrice = null;
        let variantColor = '';
        let variantSize = '';
        let variantBarcode = '';
        if(variants.length === 1){
            variantId = variants[0].id;
            variantColor = variants[0].color || '';
            variantSize = variants[0].size || '';
            variantBarcode = variants[0].barcode || '';
            variantLabelText = (variantColor + ' ' + variantSize).trim();
            if(variants[0].price){
                variantPrice = variants[0].price;
            }
        }
        row.querySelector('.quotation-variant-id').value = variantId;
        row.querySelector('.quotation-variant-color').value = variantColor;
        row.querySelector('.quotation-variant-size').value = variantSize;
        row.querySelector('.quotation-variant-barcode').value = variantBarcode;
        if(variantLabel){
            variantLabel.textContent = variantLabelText;
        }

        if(priceInput){
            const basePrice = variantPrice !== null ? variantPrice : (product.price || 0);
            priceInput.value = Number(basePrice || 0).toFixed(2);
        }
        if(taxInput && !taxInput.value){
            taxInput.value = 0;
        }
        recalcRow(row);
    }

    function createRow(){
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="quotation-barcode-wrap">
                    <input type="text" name="item_barcode[]" class="form-control quotation-barcode"
                        placeholder="{{ __('main.barcode') ?? 'الباركود' }}">
                    <div class="quotation-suggestions list-group d-none"></div>
                </div>
                <input type="hidden" name="product_id[]" class="quotation-product-id">
                <input type="hidden" name="variant_id[]" class="quotation-variant-id">
                <input type="hidden" name="variant_color[]" class="quotation-variant-color">
                <input type="hidden" name="variant_size[]" class="quotation-variant-size">
                <input type="hidden" name="variant_barcode[]" class="quotation-variant-barcode">
            </td>
            <td>
                <div class="quotation-product-name text-muted">--</div>
                <div class="small text-muted quotation-variant-label"></div>
            </td>
            <td><input type="number" step="0.01" name="qnt[]" class="form-control qnt" value="1"></td>
            <td><input type="number" step="0.01" name="price[]" class="form-control price" value="0"></td>
            <td><input type="number" step="0.01" name="tax[]" class="form-control tax" value="0"></td>
            <td><input type="number" step="0.01" class="form-control total" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
        `;
        itemsTableBody.appendChild(row);
        bindBarcodeInput(row.querySelector('.quotation-barcode'));
        recalcRow(row);
        return row;
    }

    function findEmptyRow(){
        const rows = itemsTableBody.querySelectorAll('tr');
        for (const row of rows) {
            const productId = row.querySelector('.quotation-product-id');
            if(productId && !productId.value){
                return row;
            }
        }
        return null;
    }

    function showRowSuggestions(row, products){
        const suggestions = row.querySelector('.quotation-suggestions');
        if(!suggestions){
            return;
        }
        suggestions.innerHTML = '';
        products.forEach(function(product){
            const label = (product.name || product.code || '') + (product.code ? ' - ' + product.code : '');
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action';
            item.textContent = label;
            item.addEventListener('click', function(){
                applyProductToRow(product, row, false);
                suggestions.classList.add('d-none');
                suggestions.innerHTML = '';
            });
            suggestions.appendChild(item);
        });
        suggestions.classList.remove('d-none');
    }

    function bindBarcodeInput(input){
        if(!input){
            return;
        }
        const row = input.closest('tr');
        const suggestions = row.querySelector('.quotation-suggestions');

        input.addEventListener('input', function(){
            const raw = input.value.trim();
            if(!raw){
                if(suggestions){
                    suggestions.classList.add('d-none');
                    suggestions.innerHTML = '';
                }
                clearRowProduct(row, false);
                return;
            }
            fetchProducts(raw, function(response){
                if(response.length === 0){
                    if(suggestions){
                        suggestions.classList.add('d-none');
                        suggestions.innerHTML = '';
                    }
                    clearRowProduct(row, true);
                    row.querySelector('.quotation-product-name').textContent = 'غير موجود';
                    recalcRow(row);
                    return;
                }
                if(response.length === 1){
                    applyProductToRow(response[0], row, false);
                    if(suggestions){
                        suggestions.classList.add('d-none');
                        suggestions.innerHTML = '';
                    }
                    return;
                }
                showRowSuggestions(row, response);
            });
        });
    }

    function showQuickSuggestions(products){
        if(!quickSuggestions){
            return;
        }
        quickSuggestions.innerHTML = '';
        products.forEach(function(product){
            const label = (product.name || product.code || '') + (product.code ? ' - ' + product.code : '');
            const item = document.createElement('li');
            item.className = 'quotation-select-product';
            item.textContent = label;
            item.addEventListener('click', function(){
                addProductRow(product, false);
                quickSuggestions.style.display = 'none';
                quickSuggestions.innerHTML = '';
                if(quickInput){
                    quickInput.value = '';
                    quickInput.focus();
                }
            });
            quickSuggestions.appendChild(item);
        });
        quickSuggestions.style.display = 'block';
    }

    function addProductRow(product, forceDuplicate){
        if(!product){
            return;
        }
        if(!forceDuplicate && isDuplicateProduct(product.id, null)){
            openDuplicateModal(product, function(){
                addProductRow(product, true);
            }, function(){});
            return;
        }
        const row = findEmptyRow() || createRow();
        applyProductToRow(product, row, true);
        const qtyInput = row.querySelector('.qnt');
        if(qtyInput){
            qtyInput.focus();
            qtyInput.select();
        }
    }

    if(addRowBtn){
        addRowBtn.addEventListener('click', function(){
            const row = createRow();
            const input = row.querySelector('.quotation-barcode');
            if(input){
                input.focus();
            }
        });
    }

    if(quickInput){
        quickInput.addEventListener('input', function(){
            const code = quickInput.value.trim();
            if(!code){
                if(quickSuggestions){
                    quickSuggestions.style.display = 'none';
                    quickSuggestions.innerHTML = '';
                }
                return;
            }
            fetchProducts(code, function(response){
                if(response.length === 0){
                    if(quickSuggestions){
                        quickSuggestions.style.display = 'none';
                        quickSuggestions.innerHTML = '';
                    }
                    return;
                }
                if(response.length === 1){
                    addProductRow(response[0], false);
                    if(quickInput){
                        quickInput.value = '';
                    }
                    if(quickSuggestions){
                        quickSuggestions.style.display = 'none';
                        quickSuggestions.innerHTML = '';
                    }
                    return;
                }
                showQuickSuggestions(response);
            });
        });
    }

    document.addEventListener('click', function(event){
        if(!quickSuggestions || quickSuggestions.style.display === 'none'){
            return;
        }
        if(quickSuggestions.contains(event.target) || event.target === quickInput){
            return;
        }
        quickSuggestions.style.display = 'none';
    });

    document.addEventListener('click', function(event){
        document.querySelectorAll('.quotation-suggestions').forEach(function(list){
            if(list.classList.contains('d-none')){
                return;
            }
            if(list.contains(event.target)){
                return;
            }
            const row = list.closest('tr');
            if(row && row.contains(event.target)){
                return;
            }
            list.classList.add('d-none');
        });
    });

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

    const cancelDuplicateBtn = document.getElementById('cancelDuplicateItem');
    const confirmDuplicateBtn = document.getElementById('confirmDuplicateItem');
    if(cancelDuplicateBtn){
        cancelDuplicateBtn.addEventListener('click', function(){
            if(pendingDuplicateAction && pendingDuplicateAction.onCancel){
                pendingDuplicateAction.onCancel();
            }
            pendingDuplicateAction = null;
            $('#duplicateItemModal').modal('hide');
        });
    }
    if(confirmDuplicateBtn){
        confirmDuplicateBtn.addEventListener('click', function(){
            if(pendingDuplicateAction && pendingDuplicateAction.onConfirm){
                pendingDuplicateAction.onConfirm();
            }
            pendingDuplicateAction = null;
            $('#duplicateItemModal').modal('hide');
        });
    }

    if(itemsTableBody && itemsTableBody.children.length === 0){
        createRow();
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
