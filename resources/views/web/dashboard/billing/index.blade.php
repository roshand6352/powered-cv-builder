<x-dashboard.layout title="Billing">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Billing"
            description="View invoices, payment history, and PDF downloads."
            class="mb-0"
        />
    </x-slot>

    <div class="space-y-8">
        <section>
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="dash-section-title">PDF Downloads</h2>
                    <p class="dash-section-desc">Queued exports and download history.</p>
                </div>
            </div>

            @if ($pdfExports->isEmpty())
                <div class="dash-empty">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        @include('web.components.dashboard.icons.document')
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-navy">No PDF exports yet</h3>
                    <p class="mt-2 text-sm text-muted">Export a CV from the builder and your downloads will appear here.</p>
                    <a href="{{ route('dashboard.cvs') }}" class="dash-btn-primary mt-5">
                        Go to CVs
                    </a>
                </div>
            @else
                <div class="dash-table-wrap">
                    <div class="dash-table-scroll">
                        <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Document</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pdfExports as $export)
                                <tr>
                                    <td class="font-medium text-navy">{{ $export->filename }}</td>
                                    <td class="whitespace-nowrap text-muted">{{ $export->type->label() }}</td>
                                    <td class="whitespace-nowrap">
                                        @php
                                            $statusClasses = match ($export->status->value) {
                                                'completed' => 'dash-badge-brand',
                                                'failed' => 'dash-badge bg-red-50 text-red-700',
                                                'processing' => 'dash-badge bg-brand-100 text-brand-800',
                                                default => 'dash-badge bg-brand-50 text-brand-700',
                                            };
                                        @endphp
                                        <span class="{{ $statusClasses }}">
                                            {{ $export->status->label() }}
                                        </span>
                                        @if ($export->status->value === 'failed' && $export->error_message)
                                            <p class="mt-1 max-w-xs text-xs text-red-600">{{ $export->error_message }}</p>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap text-muted">
                                        {{ $export->created_at?->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="whitespace-nowrap text-right">
                                        @if ($export->isDownloadable())
                                            <a href="{{ route('pdf-exports.download', $export) }}" class="font-semibold text-brand-600 hover:text-brand-700">
                                                Download
                                            </a>
                                        @else
                                            <span class="text-muted/60">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>

        <section>
            <div class="mb-4">
                <h2 class="dash-section-title">Payment history</h2>
                <p class="dash-section-desc">PayFast transactions and subscription payments.</p>
            </div>

            @if ($payments->isEmpty())
                <div class="dash-empty">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        @include('web.components.dashboard.icons.receipt')
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-navy">No payments yet</h3>
                    <p class="mt-2 text-sm text-muted">Your PayFast payment history will appear here once you upgrade.</p>
                    <a href="{{ route('dashboard.subscription') }}" class="dash-btn-primary mt-5">
                        View plans
                    </a>
                </div>
            @else
                <div class="dash-table-wrap">
                    <div class="dash-table-scroll">
                        <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Plan</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="whitespace-nowrap font-medium text-navy">{{ $payment->reference ?? $payment->transaction_id }}</td>
                                    <td class="whitespace-nowrap text-muted">{{ $payment->plan?->name ?? '—' }}</td>
                                    <td class="whitespace-nowrap text-muted">{{ $payment->created_at?->format('M j, Y g:i A') }}</td>
                                    <td class="whitespace-nowrap text-muted">R{{ number_format((float) $payment->amount, 2) }}</td>
                                    <td class="whitespace-nowrap">
                                        @php
                                            $paymentStatusClasses = match ($payment->status->value) {
                                                'completed' => 'dash-badge-brand',
                                                'failed' => 'dash-badge bg-red-50 text-red-700',
                                                'cancelled' => 'dash-badge-muted',
                                                default => 'dash-badge bg-brand-100 text-brand-800',
                                            };
                                        @endphp
                                        <span class="{{ $paymentStatusClasses }}">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-dashboard.layout>
