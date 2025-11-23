@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.quotation_prefix') ?? 'عرض سعر جديد' }}</h4>
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
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>{{ __('main.clients') }}</label>
                        <select name="customer_id" class="form-control" required>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('main.warehouses') }}</label>
                        <select name="warehouse_id" class="form-control" required>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('main.note') ?? 'ملاحظات' }}</label>
                        <input type="text" name="note" class="form-control">
                    </div>
                </div>

                <div class="table-responsive">
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
@endsection

@section('js')
<script>
    const products = @json($products);

    function addRow(data = {}) {
        const tbody = document.querySelector('#itemsTable tbody');
        const index = tbody.children.length;
        const row = document.createElement('tr');

        let options = '<option value="">--</option>';
        products.forEach(p=>{
            options += `<option value="${p.id}" data-price="${p.price}" data-variants='${JSON.stringify(p.variants ?? [])}'>${p.name}</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="product_id[]" class="form-control productSelect" data-index="${index}">
                    ${options}
                </select>
                <input type="hidden" name="variant_id[]" class="variantId">
                <div class="small text-muted variant-label"></div>
            </td>
            <td><input type="number" step="0.01" name="qnt[]" class="form-control qnt" value="${data.qnt ?? 1}"></td>
            <td><input type="number" step="0.01" name="price[]" class="form-control price" value="${data.price ?? 0}"></td>
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

    addRow();
</script>
@endsection
