@extends('layouts.app')

@section('title', 'Cash Counter & Shifts')
@section('page-header', 'Cashier Counter Workspace')

@section('content')
<div class="space-y-6">

    <!-- Active Shift Status Banner -->
    @if($currentShift)
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-md border border-slate-700">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                        Active Shift: {{ $currentShift->shift_number }}
                    </span>
                    <span class="text-xs text-slate-400">Opened {{ $currentShift->opened_at->diffForHumans() }}</span>
                </div>
                <h2 class="text-2xl font-black font-heading mt-2">Cashier Drawer Summary</h2>
                <p class="text-xs text-slate-400">All received cash payments are linked automatically to this active counter shift.</p>
            </div>

            <!-- Trigger Close Shift Modal -->
            <div x-data="{ closeModal: false }">
                <button @click="closeModal = true" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span>Reconcile & Close Shift</span>
                </button>

                <!-- Close Shift Modal -->
                <div x-show="closeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                    <div @click.away="closeModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                        <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Close Cashier Shift</h3>
                        <p class="text-xs text-slate-500 mb-4">Enter physically counted drawer cash for shift {{ $currentShift->shift_number }}.</p>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-4 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Opening Float:</span>
                                <span class="font-bold text-slate-800">BDT {{ number_format($openingCash, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Cash Collections:</span>
                                <span class="font-bold text-emerald-600">+ BDT {{ number_format($cashCollected, 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-1.5 font-bold">
                                <span class="text-slate-700">Expected Closing Cash:</span>
                                <span class="text-slate-900">BDT {{ number_format($expectedClosingCash, 2) }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('shifts.close', $currentShift) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Counted Physical Cash (BDT)</label>
                                <input type="number" step="0.01" name="closing_cash_counted" required 
                                       placeholder="Enter counted cash in drawer"
                                       class="w-full rounded-lg border border-slate-300 p-2.5 text-sm font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Discrepancy Explanation (if any variance)</label>
                                <textarea name="discrepancy_reason" rows="2" placeholder="Explain any cash overage or shortage..." class="w-full rounded-lg border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"></textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="closeModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg transition-colors">
                                    Confirm Shift Closing
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shift Live Balances -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-700/80">
            <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700">
                <span class="text-[10px] uppercase font-bold text-slate-400">Opening Float</span>
                <p class="text-lg font-black text-white mt-0.5">BDT {{ number_format($openingCash, 2) }}</p>
            </div>
            <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700">
                <span class="text-[10px] uppercase font-bold text-emerald-400">Cash Received</span>
                <p class="text-lg font-black text-emerald-400 mt-0.5">BDT {{ number_format($cashCollected, 2) }}</p>
            </div>
            <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700">
                <span class="text-[10px] uppercase font-bold text-teal-300">Card & MFS Collections</span>
                <p class="text-lg font-black text-teal-300 mt-0.5">BDT {{ number_format($nonCashCollected, 2) }}</p>
            </div>
            <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700">
                <span class="text-[10px] uppercase font-bold text-amber-300">Expected In Drawer</span>
                <p class="text-lg font-black text-amber-300 mt-0.5">BDT {{ number_format($expectedClosingCash, 2) }}</p>
            </div>
        </div>
    </div>
    @else
    <!-- No Active Shift Warning & Open Trigger -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-amber-900 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold font-heading">No Active Cashier Shift</h3>
                <p class="text-xs text-amber-800 mt-0.5">You must start an official cashier shift with an opening cash float to collect cash payments.</p>
            </div>
        </div>

        <div x-data="{ openShiftModal: false }">
            <button @click="openShiftModal = true" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Open Cashier Shift Now</span>
            </button>

            <!-- Open Shift Modal -->
            <div x-show="openShiftModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="openShiftModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Start New Cashier Shift</h3>
                    <p class="text-xs text-slate-500 mb-4">Record your opening float cash received from hotel management.</p>

                    <form method="POST" action="{{ route('shifts.start') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Opening Cash Float (BDT)</label>
                            <input type="number" step="0.01" name="opening_cash" required value="10000"
                                   placeholder="e.g. 10000.00"
                                   class="w-full rounded-lg border border-slate-300 p-2.5 text-sm font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Shift Operational Notes</label>
                            <textarea name="notes" rows="2" placeholder="e.g. Morning counter shift initialized with float." class="w-full rounded-lg border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="openShiftModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors">
                                Open Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Collection Form & Outstanding Folios Search -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Fast Counter Payment Form -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 font-heading mb-1">Collect Guest Payment</h3>
            <p class="text-xs text-slate-500 mb-4">Process deposit, stay settlement, or incidentals</p>

            <form method="POST" action="{{ route('billing.payment.record') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Select Guest Folio / Invoice</label>
                    <select name="invoice_id" id="invoice_select" onchange="updateReservation(this)" required class="w-full rounded-lg border border-slate-300 p-2 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                        <option value="">-- Choose outstanding guest bill --</option>
                        @foreach($pendingInvoices as $inv)
                            <option value="{{ $inv->id }}" data-reservation-id="{{ $inv->reservation_id }}" data-balance="{{ $inv->balance_due }}">
                                {{ $inv->invoice_number }} &bull; {{ $inv->reservation->guest->full_name }} (Due: BDT {{ number_format($inv->balance_due, 2) }})
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="reservation_id" id="hidden_reservation_id">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Payment Amount (BDT)</label>
                    <input type="number" step="0.01" name="amount" id="payment_amount_input" required 
                           placeholder="0.00"
                           class="w-full rounded-lg border border-slate-300 p-2.5 text-base font-bold text-emerald-700 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Payment Method</label>
                    <select name="payment_method" required class="w-full rounded-lg border border-slate-300 p-2 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                        <option value="cash">💵 Cash In Hand</option>
                        <option value="card">💳 Credit / Debit Card (POS)</option>
                        <option value="bkash">📱 bKash Merchant</option>
                        <option value="nagad">📱 Nagad Merchant</option>
                        <option value="bank_transfer">🏦 Bank Wire / EFT</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Transaction Reference / Voucher #</label>
                    <input type="text" name="transaction_reference" placeholder="e.g. TRX-991283 or POS Approval Code" class="w-full rounded-lg border border-slate-300 p-2 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Cashier Memo</label>
                    <input type="text" name="notes" placeholder="e.g. Settled upon departure" class="w-full rounded-lg border border-slate-300 p-2 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>

                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Record Payment & Issue Receipt</span>
                </button>
            </form>
        </div>

        <!-- Recent Payments in this Shift -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-heading">Payments Recorded in Shift</h3>
                        <p class="text-xs text-slate-500">Live cashier collection log with printable receipt generation</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $shiftPayments->count() }} Payments
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Receipt #</th>
                                <th class="py-3 px-3">Guest / Res</th>
                                <th class="py-3 px-3">Amount</th>
                                <th class="py-3 px-3">Method</th>
                                <th class="py-3 px-3">Time</th>
                                <th class="py-3 px-3 text-center">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($shiftPayments as $sp)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $sp->payment_number }}</td>
                                <td class="py-3 px-3 text-slate-800">{{ $sp->reservation->guest->full_name }}</td>
                                <td class="py-3 px-3 font-bold text-emerald-700">BDT {{ number_format($sp->amount, 2) }}</td>
                                <td class="py-3 px-3 uppercase text-[10px] font-bold text-slate-700">{{ $sp->payment_method }}</td>
                                <td class="py-3 px-3 text-slate-400">{{ $sp->paid_at->format('H:i') }}</td>
                                <td class="py-3 px-3 text-center">
                                    <a href="{{ route('billing.payment.receipt', $sp) }}" target="_blank" class="px-2 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded">
                                        Print Receipt
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">No payments recorded in this shift yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Shift Total Collected:</span>
                <span class="font-extrabold text-slate-900 text-sm">BDT {{ number_format($cashCollected + $nonCashCollected, 2) }}</span>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function updateReservation(selectElem) {
        const option = selectElem.options[selectElem.selectedIndex];
        if (option) {
            const resId = option.getAttribute('data-reservation-id');
            const balance = option.getAttribute('data-balance');
            document.getElementById('hidden_reservation_id').value = resId || '';
            if (balance) {
                document.getElementById('payment_amount_input').value = balance;
            }
        }
    }
</script>
@endpush
@endsection
