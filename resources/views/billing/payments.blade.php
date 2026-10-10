@extends('layouts.app')

@section('title', 'Payment Ledger')
@section('page-header', 'Financial Payment Ledger')

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('billing.payments') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative min-w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by payment #, voucher ref, guest..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>

            <select name="method" onchange="this.form.submit()" class="rounded-xl border border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700">
                <option value="">All Payment Methods</option>
                <option value="cash" {{ $method === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ $method === 'card' ? 'selected' : '' }}>Card (POS)</option>
                <option value="bkash" {{ $method === 'bkash' ? 'selected' : '' }}>bKash</option>
                <option value="nagad" {{ $method === 'nagad' ? 'selected' : '' }}>Nagad</option>
                <option value="bank_transfer" {{ $method === 'bank_transfer' ? 'selected' : '' }}>Bank Wire</option>
            </select>

            <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Filter
            </button>
            @if($search || $method)
            <a href="{{ route('billing.payments') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>

        <a href="{{ route('billing.invoices') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors shrink-0">
            &larr; View Guest Invoices
        </a>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Receipt / Pay #</th>
                        <th class="py-3 px-4">Guest Information</th>
                        <th class="py-3 px-4">Branch Context</th>
                        <th class="py-3 px-4">Method & Channel</th>
                        <th class="py-3 px-4">Transaction Ref</th>
                        <th class="py-3 px-4 text-right">Amount (BDT)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Recorded By / Date</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payments as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                            {{ $p->payment_number }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block">{{ $p->reservation->guest->full_name }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $p->reservation->reservation_number }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $p->branch->name }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-800">
                                {{ strtoupper($p->payment_method) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">{{ $p->transaction_reference ?? '—' }}</td>
                        <td class="py-3.5 px-4 text-right font-black text-emerald-700 text-sm">
                            BDT {{ number_format($p->amount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $p->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500">
                            <span class="block">{{ $p->paid_at->format('M d, Y H:i') }}</span>
                            <span class="text-[11px] text-slate-400">By {{ $p->receivedBy?->name ?? 'System' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5" x-data="{ refundModal: false }">
                                <a href="{{ route('billing.payment.receipt', $p) }}" target="_blank" class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">
                                    Receipt
                                </a>

                                @if($p->status === 'completed' && auth()->user()->hasPermission('payments.collect'))
                                <button @click="refundModal = true" class="px-2 py-1 text-[10px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded">
                                    Refund
                                </button>

                                <!-- Refund Modal -->
                                <div x-show="refundModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 text-left">
                                    <div @click.away="refundModal = false" class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-slate-800">
                                        <h3 class="text-base font-bold text-slate-900 font-heading mb-1">Request Payment Refund</h3>
                                        <p class="text-xs text-slate-500 mb-4">Payment #{{ $p->payment_number }} (Max: BDT {{ $p->amount }})</p>

                                        <form method="POST" action="{{ route('billing.refund.request') }}" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="payment_id" value="{{ $p->id }}">
                                            <div>
                                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Refund Amount (BDT)</label>
                                                <input type="number" step="0.01" name="amount" value="{{ $p->amount }}" max="{{ $p->amount }}" required class="w-full rounded-xl border border-slate-300 p-2 text-xs font-bold">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Reason for Refund</label>
                                                <textarea name="reason" required rows="2" placeholder="State operational reason..." class="w-full rounded-xl border border-slate-300 p-2 text-xs"></textarea>
                                            </div>
                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="refundModal = false" class="px-3 py-1.5 text-xs font-semibold text-slate-600">Cancel</button>
                                                <button type="submit" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-bold">Submit Request</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">No payment transactions recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
    </div>

</div>
@endsection
