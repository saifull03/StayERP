@extends('layouts.app')

@section('title', 'Cashier Shifts')
@section('page-header', 'Cashier Shifts & Reconciliation')

@section('content')
<div class="space-y-6">

    <!-- Active Shift Reminder or Start Button -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">Shift Management & Reconciliation Ledger</h2>
            <p class="text-xs text-slate-500">Track opening float, continuous cash collections, and closing drawer variances</p>
        </div>

        @if($currentOpenShift)
        <a href="{{ route('dashboard.cashier') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
            <span>Manage Active Shift ({{ $currentOpenShift->shift_number }})</span>
        </a>
        @else
        <div x-data="{ openShiftModal: false }">
            <button @click="openShiftModal = true" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Open New Shift</span>
            </button>

            <!-- Open Shift Modal -->
            <div x-show="openShiftModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="openShiftModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Start Cashier Shift</h3>
                    <p class="text-xs text-slate-500 mb-4">Record your opening float cash received from hotel management.</p>

                    <form method="POST" action="{{ route('shifts.start') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Opening Cash Float (BDT) *</label>
                            <input type="number" step="0.01" name="opening_cash" required value="10000" class="w-full rounded-xl border border-slate-300 p-2.5 text-sm font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Shift Notes</label>
                            <textarea name="notes" rows="2" placeholder="e.g. Afternoon counter shift" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="openShiftModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg">Start Shift</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Shifts Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Shift Number</th>
                        <th class="py-3 px-4">Cashier</th>
                        <th class="py-3 px-4">Branch</th>
                        <th class="py-3 px-4">Opened At</th>
                        <th class="py-3 px-4">Closed At</th>
                        <th class="py-3 px-4 text-right">Opening Float</th>
                        <th class="py-3 px-4 text-right">Counted Cash</th>
                        <th class="py-3 px-4 text-right">Variance / Discrepancy</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Reconciliation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($shifts as $s)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                            <a href="{{ route('shifts.show', $s) }}" class="hover:text-emerald-700">{{ $s->shift_number }}</a>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $s->user->name }}</td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $s->branch->name }}</td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $s->opened_at->format('M d, H:i') }}</td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $s->closed_at ? $s->closed_at->format('M d, H:i') : '—' }}</td>
                        <td class="py-3.5 px-4 text-right text-slate-800">BDT {{ number_format($s->opening_cash, 2) }}</td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                            {{ $s->closing_cash_counted !== null ? 'BDT ' . number_format($s->closing_cash_counted, 2) : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-extrabold {{ $s->cash_discrepancy < 0 ? 'text-rose-600' : ($s->cash_discrepancy > 0 ? 'text-emerald-600' : 'text-slate-500') }}">
                            @if($s->status === 'closed')
                                {{ $s->cash_discrepancy == 0 ? 'Balanced' : ($s->cash_discrepancy > 0 ? '+' : '') . 'BDT ' . number_format($s->cash_discrepancy, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $s->status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('shifts.show', $s) }}" class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">
                                Summary
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-8 text-center text-slate-400">No shift records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $shifts->links() }}
        </div>
    </div>

</div>
@endsection
