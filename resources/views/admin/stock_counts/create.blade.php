@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.inventory') ?? 'جرد المخزون' }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('stock_counts.index') }}" class="btn btn-secondary">{{ __('main.back') ?? 'عودة' }}</a>
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
            <form method="POST" action="{{ route('stock_counts.store') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>{{ __('main.warehouses') }}</label>
                        <select name="warehouse_id" class="form-control" required>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label>{{ __('main.note') ?? 'ملاحظات' }}</label>
                        <input type="text" name="note" class="form-control">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                        <tr>
                            <th>{{ __('main.item') }}</th>
                            <th>{{ __('main.quantity') }} (Expected)</th>
                            <th>{{ __('main.quantity') }} (Counted)</th>
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
            options += `<option value="${p.id}" data-variants='${JSON.stringify(p.variants ?? [])}' ${data.product_id==p.id?'selected':''}>${p.name}</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="items[${index}][product_id]" class="form-control productSelect">
                    ${options}
                </select>
                <input type="hidden" name="items[${index}][variant_id]" class="variantId">
                <div class="small text-muted variant-label"></div>
            </td>
            <td><input type="number" step="0.01" name="items[${index}][expected_qty]" class="form-control expected" readonly value="${data.expected_qty ?? ''}"></td>
            <td><input type="number" step="0.01" name="items[${index}][counted_qty]" class="form-control counted" value="${data.counted_qty ?? 0}"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
        `;
        tbody.appendChild(row);
    }

    document.getElementById('addRowBtn').addEventListener('click', ()=>addRow());

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
                const variantInput = e.target.closest('td').querySelector('.variantId');
                variantInput.value = variantId;
            }
            e.target.closest('td').querySelector('.variant-label').innerText = label;
        }
    });

    addRow();
</script>
@endsection
