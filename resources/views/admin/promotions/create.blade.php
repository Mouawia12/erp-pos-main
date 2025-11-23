@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.promotions') ?? 'العروض الترويجية' }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('promotions.index') }}" class="btn btn-secondary">{{ __('main.back') ?? 'عودة' }}</a>
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
            <form method="POST" action="{{ route('promotions.store') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>{{ __('main.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.start_date') ?? 'تاريخ البداية' }}</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('main.end_date') ?? 'تاريخ النهاية' }}</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('main.status') }}</label>
                        <select name="status" class="form-control">
                            <option value="active">{{ __('main.status1') ?? 'مفعل' }}</option>
                            <option value="inactive">{{ __('main.status2') ?? 'موقوف' }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>{{ __('main.note') ?? 'ملاحظات' }}</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                        <tr>
                            <th>{{ __('main.item') }}</th>
                            <th>{{ __('main.quantity') }}</th>
                            <th>{{ __('main.discount') }}</th>
                            <th>{{ __('main.discount') }} %</th>
                            <th>{{ __('main.max_order') ?? 'أقصى كمية' }}</th>
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
            <td><input type="number" step="0.01" name="items[${index}][min_qty]" class="form-control" value="${data.min_qty ?? 1}"></td>
            <td><input type="number" step="0.01" name="items[${index}][discount_value]" class="form-control" value="${data.discount_value ?? 0}"></td>
            <td>
                <select name="items[${index}][discount_type]" class="form-control">
                    <option value="percent" ${data.discount_type==='percent'?'selected':''}>%</option>
                    <option value="amount" ${data.discount_type==='amount'?'selected':''}>{{ __('main.Amount') ?? 'قيمة' }}</option>
                </select>
            </td>
            <td><input type="number" step="0.01" name="items[${index}][max_qty]" class="form-control" value="${data.max_qty ?? ''}"></td>
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
