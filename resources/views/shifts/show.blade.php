@extends('layouts.app')

@section('title', 'Shift Reconciliation ' . $shift->shift_number)
@section('page-header')
    Shift Reconciliation #{{ $shift->shift_number }}
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('shifts.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Shifts</span>
        </a>
        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $shift->status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-800' }}">
            {{ $shift->status }} Shift
        </span>
    </div>

    <!-- Reconciliation Sheet -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 text-slate-800 space-y-6">
        
        <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 font-heading">Cashier Shift Closing Audit Report</h2>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $shift->branch->name }} &bull; Cashier: <span class="font-bold text-slate-800">{{ $shift->user->name }}</span>
                </p>
            </div>
            <div class="text-xs text-slate-500 sm:text-right font-mono">
                <p>Opened: {{ $shift->opened_at->format('Y-m-d H:i') }}</p>
                <p>Closed: {{ $shift->closed_at ? $shift->closed_at->format('Y-m-d H:i') : 'Active' }}</p>
            </div>
        </div>

        <!-- Cash Reconciliation Formula Breakdown -->
        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-3 text-xs">
            <h3 class="font-bold text-slate-900 uppercase text-xs tracking-wider">Cash Drawer Reconciliation Equation</h3>

            <div class="flex justify-between py-1 border-b border-slate-200/60">
                <span class="text-slate-600 font-medium">(+) Initial Opening Float Cash:</span>
                <span class="font-bold text-slate-900">BDT {{ number_format($shift->opening_cash, 2) }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-200/60">
                <span class="text-slate-600 font-medium">(+) Cash Collections In Shift:</span>
                <span class="font-bold text-emerald-700">+ BDT {{ number_format($shift->cash_collected, 2) }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-200/60">
                <span class="text-slate-600 font-medium">(-) Cash Paid Expenses from Drawer:</span>
                <span class="font-bold text-rose-600">- BDT {{ number_format($shift->cash_expenses, 2) }}</span>
            </div>

            <div class="flex justify-between py-2 border-t-2 border-slate-300 font-bold text-sm text-slate-900">
                <span>(=) Expected Physical Cash in Drawer:</span>
                <span class="font-black">BDT {{ number_format($shift->expected_closing_cash ?? $shift->calculateExpectedCash(), 2) }}</span>
            </div>

            <div class="flex justify-between py-1">
                <span class="text-slate-700 font-bold">Physically Counted & Submitted Cash:</span>
                <span class="font-black text-slate-900">
                    {{ $shift->closing_cash_counted !== null ? 'BDT ' . number_format($shift->closing_cash_counted, 2) : 'Awaiting Count' }}
                </span>
            </div>

            <div class="flex justify-between py-2 border-t-2 border-slate-800 text-sm font-black">
                <span>Cash Drawer Variance / Discrepancy:</span>
                <span class="{{ $shift->cash_discrepancy < 0 ? 'text-rose-600' : ($shift->cash_discrepancy > 0 ? 'text-emerald-600' : 'text-slate-800') }}">
                    @if($shift->status === 'closed')
                        {{ $shift->cash_discrepancy == 0 ? 'BALANCED (BDT 0.00)' : ($shift->cash_discrepancy > 0 ? '+' : '') . 'BDT ' . number_format($shift->cash_discrepancy, 2) }}
                    @else
                        Pending Closing
                    @endif
                </span>
            </div>

            @if($shift->discrepancy_reason)
            <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900">
                <span class="font-bold block mb-0.5">Discrepancy Explanation Note:</span>
                {{ $shift->discrepancy_reason }}
            </div>
            @endif
        </div>

        <!-- Non-Cash Receipts (POS, Bank, MFS) -->
        <div>
            <h3 class="text-sm font-bold text-slate-900 font-heading mb-2">Non-Cash Digital & Card Transactions (POS / MFS)</h3>
            <p class="text-xs text-slate-500 mb-3">These electronic settlements bypass physical cash drawer float.</p>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs flex justify-between font-bold">
                <span class="text-slate-600">Total Non-Cash Digital Collections:</span>
                <span class="text-slate-900">BDT {{ number_format($shift->non_cash_collected, 2) }}</span>
            </div>
        </div>

        <!-- Payments Log in this Shift -->
        <div>
            <h3 class="text-sm font-bold text-slate-900 font-heading mb-3">Itemized Payments Collected During Shift</h3>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Receipt #</th>
                            <th class="py-2.5 px-3">Guest / Res</th>
                            <th class="py-2.5 px-3">Method</th>
                            <th class="py-2.5 px-3 text-right">Amount (BDT)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($shift->payments as $p)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-bold">{{ $p->payment_number }}</td>
                            <td class="py-2.5 px-3">{{ $p->reservation->guest->full_name }}</td>
                            <td class="py-2.5 px-3 uppercase text-[10px]">{{ $p->payment_method }}</td>
                            <td class="py-2.5 px-3 text-right font-bold text-emerald-700">BDT {{ number_format($p->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-400">No payments in this shift.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
