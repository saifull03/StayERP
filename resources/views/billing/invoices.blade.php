@extends('layouts.app')

@section('title', 'Guest Invoices')
@section('page-header', 'Tax Invoices & Billing Folios')

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('billing.invoices') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative min-w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by invoice #, guest name..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>

            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700">
                <option value="">All Billing Statuses</option>
                <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="partially_paid" {{ $status === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Fully Settled (Paid)</option>
                <option value="void" {{ $status === 'void' ? 'selected' : '' }}>Void</option>
            </select>

            <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Filter
            </button>
            @if($search || $status)
            <a href="{{ route('billing.invoices') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>

        <a href="{{ route('billing.payments') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors shrink-0">
            View Payment Ledger &rarr;
        </a>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Invoice #</th>
                        <th class="py-3 px-4">Guest / Client</th>
                        <th class="py-3 px-4">Branch</th>
                        <th class="py-3 px-4">Issued Date</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                        <th class="py-3 px-4 text-right">Tax (VAT)</th>
                        <th class="py-3 px-4 text-right">Grand Total</th>
                        <th class="py-3 px-4 text-right">Balance Due</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                            <a href="{{ route('billing.invoice.show', $inv) }}" class="hover:text-emerald-700">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $inv->reservation->guest->full_name }}</td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $inv->branch->name }}</td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $inv->issued_date->format('M d, Y') }}</td>
                        <td class="py-3.5 px-4 text-right text-slate-700">BDT {{ number_format($inv->subtotal, 2) }}</td>
                        <td class="py-3.5 px-4 text-right text-slate-500">BDT {{ number_format($inv->tax_amount, 2) }}</td>
                        <td class="py-3.5 px-4 text-right font-black text-slate-900">BDT {{ number_format($inv->grand_total, 2) }}</td>
                        <td class="py-3.5 px-4 text-right font-bold {{ $inv->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                            BDT {{ number_format($inv->balance_due, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $badgeClasses = [
                                    'paid' => 'bg-emerald-100 text-emerald-800',
                                    'partially_paid' => 'bg-amber-100 text-amber-800',
                                    'unpaid' => 'bg-rose-100 text-rose-800',
                                    'void' => 'bg-slate-200 text-slate-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClasses[$inv->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ str_replace('_', ' ', $inv->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('billing.invoice.show', $inv) }}" class="px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                Print Invoice
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-8 text-center text-slate-400">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $invoices->links() }}
        </div>
    </div>

</div>
@endsection
