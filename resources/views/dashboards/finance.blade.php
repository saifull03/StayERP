@extends('layouts.app')

@section('title', 'Finance & Accounts')
@section('page-header', 'Finance & Revenue Management')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Payments Collected -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Payments Inflow</span>
            <p class="text-2xl font-black text-emerald-700 mt-1 font-heading">BDT {{ number_format($totalPayments, 2) }}</p>
            <span class="text-xs text-slate-400 font-medium">Reconciled completed payments</span>
        </div>

        <!-- Receivables -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Outstanding Balances</span>
            <p class="text-2xl font-black text-amber-600 mt-1 font-heading">BDT {{ number_format($outstandingBalances, 2) }}</p>
            <span class="text-xs text-slate-400 font-medium">Accounts receivable on folios</span>
        </div>

        <!-- Expenses Approved -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Approved Operating Expenses</span>
            <p class="text-2xl font-black text-slate-900 mt-1 font-heading">BDT {{ number_format($approvedExpenses, 2) }}</p>
            <span class="text-xs text-rose-600 font-semibold">Pending approval: BDT {{ number_format($pendingExpenses, 2) }}</span>
        </div>

        <!-- Refunds Processed -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Authorized Refunds</span>
            <p class="text-2xl font-black text-rose-600 mt-1 font-heading">BDT {{ number_format($refundsTotal, 2) }}</p>
            <span class="text-xs text-slate-400 font-medium">Processed reversals & refunds</span>
        </div>
    </div>

    <!-- Revenue Breakdown by Payment Method -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 font-heading mb-1">Collections Breakdown by Payment Channel</h3>
        <p class="text-xs text-slate-500 mb-4">Granular method distribution for bank reconciliation and audit</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($revenueByMethod as $method => $amount)
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60 flex flex-col justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">{{ strtoupper(str_replace('_', ' ', $method)) }}</span>
                <p class="text-base font-black text-slate-900 mt-1 font-heading">BDT {{ number_format($amount, 2) }}</p>
                <div class="w-full bg-slate-200 rounded-full h-1 mt-2 overflow-hidden">
                    <div class="bg-emerald-600 h-1 rounded-full" style="width: {{ $totalPayments > 0 ? min(100, round(($amount / $totalPayments) * 100)) : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Invoices & Transactions Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Invoices -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200/80 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 font-heading">Recent Invoices</h3>
                <a href="{{ route('billing.invoices') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                    All Invoices &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-4">Invoice #</th>
                            <th class="py-2.5 px-3">Guest</th>
                            <th class="py-2.5 px-3 text-right">Total</th>
                            <th class="py-2.5 px-3 text-right">Balance</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($recentInvoices as $inv)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('billing.invoice.show', $inv) }}" class="hover:text-emerald-700">{{ $inv->invoice_number }}</a>
                            </td>
                            <td class="py-3 px-3 text-slate-800">{{ $inv->reservation->guest->full_name }}</td>
                            <td class="py-3 px-3 text-right font-bold text-slate-900">BDT {{ number_format($inv->grand_total, 2) }}</td>
                            <td class="py-3 px-3 text-right font-bold {{ $inv->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                BDT {{ number_format($inv->balance_due, 2) }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">
                                    {{ str_replace('_', ' ', $inv->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">No invoices recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Completed Payments -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200/80 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 font-heading">Recent Payment Ledger</h3>
                <a href="{{ route('billing.payments') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                    Payment Ledger &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-4">Receipt #</th>
                            <th class="py-2.5 px-3">Amount</th>
                            <th class="py-2.5 px-3">Method</th>
                            <th class="py-2.5 px-3">Time</th>
                            <th class="py-2.5 px-3 text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($recentPayments as $p)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $p->payment_number }}</td>
                            <td class="py-3 px-3 font-bold text-emerald-700">BDT {{ number_format($p->amount, 2) }}</td>
                            <td class="py-3 px-3 uppercase text-[11px] font-semibold text-slate-700">{{ $p->payment_method }}</td>
                            <td class="py-3 px-3 text-slate-400">{{ $p->paid_at->format('M d, H:i') }}</td>
                            <td class="py-3 px-3 text-center">
                                <a href="{{ route('billing.payment.receipt', $p) }}" target="_blank" class="px-2 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded">
                                    Receipt
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">No payment records available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
