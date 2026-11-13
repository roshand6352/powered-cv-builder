<x-dashboard.layout title="Billing">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Billing"
            description="View invoices, payment history, and your document library."
            class="mb-0"
        />
    </x-slot>

    <div class="space-y-8">
        <section>
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="dash-section-title">Document Library</h2>
                    <p class="dash-section-desc">Every CV and cover letter PDF you download is saved here, so you can download it again anytime.</p>
                </div>
            </div>

            @php
                $libraryItems = $pdfExports->map(function ($export) {
                    return (object) [
                        'export' => $export,
                        'title' => $export->exportable?->title ?: $export->filename,
                        'icon' => $export->type === \App\Enums\PdfExportType::CoverLetter ? 'mail' : 'document',
                        'statusClasses' => match ($export->status->value) {
                            'completed' => 'dash-badge-brand',
                            'failed' => 'dash-badge bg-red-50 text-red-700',
                            'processing' => 'dash-badge bg-brand-100 text-brand-800',
                            default => 'dash-badge bg-brand-50 text-brand-700',
                        },
                        'date' => ($export->completed_at ?? $export->created_at)?->format('M j, Y g:i A'),
                        'size' => $export->file_size ? \Illuminate\Support\Number::fileSize($export->file_size, precision: 1) : null,
                        'downloadUrl' => $export->isDownloadable() ? route('pdf-exports.download', $export) : null,
                    ];
                });
            @endphp

            @if ($libraryItems->isEmpty())
                <div class="dash-empty">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        @include('web.components.dashboard.icons.document')
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-navy">No documents yet</h3>
                    <p class="mt-2 text-sm text-muted">Download a CV or cover letter as a PDF and it will be saved here for later.</p>
                    <a href="{{ route('dashboard.cvs') }}" class="dash-btn-primary mt-5">
                        Go to CVs
                    </a>
                </div>
            @else
                {{-- Mobile / tablet: document cards --}}
                <ul class="space-y-3 md:hidden">
                    @foreach ($libraryItems as $item)
                        <li class="dash-card p-4">
                            <div class="flex items-start gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                    @include('web.components.dashboard.icons.' . $item->icon)
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="break-words font-semibold text-navy">{{ $item->title }}</p>
                                    <p class="mt-0.5 break-all text-xs text-muted">{{ $item->export->filename }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted">
                                        <span class="{{ $item->statusClasses }}">{{ $item->export->status->label() }}</span>
                                        <span>{{ $item->export->type->label() }}</span>
                                        @if ($item->size)
                                            <span aria-hidden="true">&middot;</span>
                                            <span>{{ $item->size }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-muted">{{ $item->date }}</p>
                                    @if ($item->export->status->value === 'failed' && $item->export->error_message)
                                        <p class="mt-1 break-words text-xs text-red-600">{{ $item->export->error_message }}</p>
                                    @endif
                                </div>
                            </div>

                            @if ($item->downloadUrl)
                                <a href="{{ $item->downloadUrl }}" class="dash-btn-secondary mt-4 w-full gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3" />
                                    </svg>
                                    Download PDF
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>

                {{-- Desktop: table --}}
                <div class="dash-table-wrap hidden md:block">
                    <div class="dash-table-scroll">
                        <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Document</th>
                                <th class="hidden xl:table-cell">Type</th>
                                <th>Status</th>
                                <th>Generated</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($libraryItems as $item)
                                <tr>
                                    <td class="min-w-[15rem] max-w-xs">
                                        <p class="break-words font-medium text-navy">{{ $item->title }}</p>
                                        <p class="mt-0.5 break-all text-xs text-muted">
                                            <span class="xl:hidden">{{ $item->export->type->label() }} &middot; </span>{{ $item->export->filename }}@if ($item->size) &middot; {{ $item->size }}@endif
                                        </p>
                                    </td>
                                    <td class="hidden whitespace-nowrap text-muted xl:table-cell">{{ $item->export->type->label() }}</td>
                                    <td class="whitespace-nowrap">
                                        <span class="{{ $item->statusClasses }}">
                                            {{ $item->export->status->label() }}
                                        </span>
                                        @if ($item->export->status->value === 'failed' && $item->export->error_message)
                                            <p class="mt-1 max-w-xs whitespace-normal text-xs text-red-600">{{ $item->export->error_message }}</p>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap text-muted">
                                        {{ $item->date }}
                                    </td>
                                    <td class="whitespace-nowrap text-right">
                                        @if ($item->downloadUrl)
                                            <a href="{{ $item->downloadUrl }}" class="font-semibold text-brand-600 hover:text-brand-700">
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
