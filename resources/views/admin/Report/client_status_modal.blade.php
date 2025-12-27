<div class="modal fade" id="company_status_modal" tabindex="-1" role="dialog" aria-labelledby="companyStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="companyStatusLabel">
                    {{ __('main.client_status_report') ?? 'تقارير العملاء حسب الحالة' }}
                </h5>
                <button type="button" class="close cancel-modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2">{{ $period ?? '' }}</p>
                @if(!empty($invoiceNo))
                    <p class="mb-2">{{ __('main.invoice_no') }}: {{ $invoiceNo }}</p>
                @endif
                @if($amountMin !== null && $amountMin !== '' || $amountMax !== null && $amountMax !== '')
                    <p class="mb-2">
                        {{ __('main.amount_range') ?? 'نطاق القيمة' }}:
                        {{ $amountMin !== null && $amountMin !== '' ? $amountMin : '-' }}
                        -
                        {{ $amountMax !== null && $amountMax !== '' ? $amountMax : '-' }}
                    </p>
                @endif

                @php $companyMap = $companies->keyBy('id'); @endphp
                @if($status === 'active')
                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>{{ __('main.clients') }}</th>
                            <th>{{ __('main.invoice_no') }}</th>
                            <th>{{ __('main.date') }}</th>
                            <th>{{ __('main.total_balance') ?? 'القيمة' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($details as $row)
                            @php $company = $companyMap->get($row->company_id); @endphp
                            <tr>
                                <td>{{ $company?->name ?? '-' }}</td>
                                <td>{{ $row->invoice_no }}</td>
                                <td>{{ $row->date }}</td>
                                <td>{{ number_format($row->net ?? $row->total ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">{{ __('main.no_data') ?? 'لا توجد بيانات' }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>{{ __('main.clients') }}</th>
                            <th>{{ __('main.status') }}</th>
                            <th>{{ __('main.invoice_no') }}</th>
                            <th>{{ __('main.date') }}</th>
                            <th>{{ __('main.total_balance') ?? 'القيمة' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($companies as $company)
                            @php $last = $lastTransactions->get($company->id); @endphp
                            <tr>
                                <td>{{ $company->name }}</td>
                                <td>
                                    @if($company->stop_sale)
                                        {{ __('main.status_stopped') ?? 'موقوف' }}
                                    @else
                                        {{ __('main.status_inactive') ?? 'راكد' }}
                                    @endif
                                </td>
                                <td>{{ $last?->invoice_no ?? '-' }}</td>
                                <td>{{ $last?->date ?? '-' }}</td>
                                <td>{{ number_format($last?->net ?? $last?->total ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">{{ __('main.no_data') ?? 'لا توجد بيانات' }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
