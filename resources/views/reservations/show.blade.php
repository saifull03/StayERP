@extends('layouts.app')

@section('title', 'Reservation ' . $reservation->reservation_number)
@section('page-header')
    Reservation {{ $reservation->reservation_number }}
@endsection

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('reservations.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black text-slate-900 font-heading">{{ $reservation->reservation_number }}</h2>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-100 text-amber-800',
                            'confirmed' => 'bg-blue-100 text-blue-800',
                            'checked_in' => 'bg-emerald-100 text-emerald-800',
                            'checked_out' => 'bg-slate-100 text-slate-800',
                            'cancelled' => 'bg-rose-100 text-rose-800',
                        ];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusClasses[$reservation->status] ?? 'bg-slate-100 text-slate-800' }}">
                        {{ str_replace('_', ' ', $reservation->status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ $reservation->branch->name }} &bull; Booked {{ $reservation->created_at->format('M d, Y') }} via {{ strtoupper($reservation->source) }}</p>
            </div>
        </div>

        <!-- State Machine Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @if(in_array($reservation->status, ['confirmed', 'pending']))
            <form method="POST" action="{{ route('reservations.checkin', $reservation) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Check In Guest</span>
                </button>
            </form>
            @endif

            @if($reservation->status === 'checked_in')
            <form method="POST" action="{{ route('reservations.checkout', $reservation) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                    <i data-lucide="door-open" class="w-4 h-4"></i>
                    <span>Complete Check Out</span>
                </button>
            </form>
            @endif

            @if(!in_array($reservation->status, ['checked_out', 'cancelled']))
            <div x-data="{ cancelModal: false }">
                <button @click="cancelModal = true" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition-colors">
                    Cancel Stay
                </button>

                <!-- Cancel Modal -->
                <div x-show="cancelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                    <div @click.away="cancelModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                        <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Cancel Reservation</h3>
                        <p class="text-xs text-slate-500 mb-4">Please provide a valid cancellation reason for audit purposes.</p>

                        <form method="POST" action="{{ route('reservations.cancel', $reservation) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Cancellation Reason *</label>
                                <textarea name="cancellation_reason" required rows="3" placeholder="e.g. Guest travel schedule changed" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs"></textarea>
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="cancelModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">
                                    Keep Reservation
                                </button>
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg transition-colors">
                                    Confirm Cancellation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Main 2-Column Info & Folio -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Guest & Stay Details -->
        <div class="space-y-6">
            
            <!-- Guest Profile Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Guest Information</h3>
                    @if($reservation->guest->vip_status)
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-400 text-slate-900">VIP GUEST</span>
                    @endif
                </div>

                <div>
                    <h4 class="text-base font-black text-slate-900 font-heading">{{ $reservation->guest->full_name }}</h4>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>{{ $reservation->guest->phone }}</span>
                    </p>
                    @if($reservation->guest->email)
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>{{ $reservation->guest->email }}</span>
                    </p>
                    @endif
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <i data-lucide="id-card" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>{{ strtoupper($reservation->guest->id_type) }}: {{ $reservation->guest->id_number ?? 'Not Recorded' }}</span>
                    </p>
                </div>
            </div>

            <!-- Stay Summary Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3 text-xs">
                <h3 class="font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Stay Parameters</h3>

                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Check-In Date:</span>
                    <span class="font-bold text-slate-800">{{ $reservation->check_in_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Check-Out Date:</span>
                    <span class="font-bold text-slate-800">{{ $reservation->check_out_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Total Nights:</span>
                    <span class="font-bold text-slate-800">{{ $reservation->nights_count }} Night(s)</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Occupants:</span>
                    <span class="font-bold text-slate-800">{{ $reservation->adults }} Adults, {{ $reservation->children }} Children</span>
                </div>
                <div class="flex justify-between py-1 border-t border-slate-100 pt-2">
                    <span class="text-slate-500">Assigned Room:</span>
                    @foreach($reservation->reservationRooms as $rr)
                    <span class="font-black text-slate-900">Room {{ $rr->room?->room_number ?? 'TBD' }} ({{ $rr->roomType->name }})</span>
                    @endforeach
                </div>
                @if($reservation->special_requests)
                <div class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600">
                    <span class="font-bold block text-slate-800">Special Notes:</span>
                    {{ $reservation->special_requests }}
                </div>
                @endif
            </div>

            <!-- Balance Due Widget -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-5 rounded-2xl text-white shadow-xs space-y-3">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Account Billing Status</span>
                <div class="flex items-baseline justify-between">
                    <span class="text-xs text-slate-300">Total Charges:</span>
                    <span class="text-sm font-bold">BDT {{ number_format($reservation->total_charges, 2) }}</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-xs text-slate-300">Total Paid / Deposits:</span>
                    <span class="text-sm font-bold text-emerald-400">BDT {{ number_format($reservation->total_paid, 2) }}</span>
                </div>
                <div class="flex items-baseline justify-between border-t border-slate-700 pt-2">
                    <span class="text-xs font-bold">Balance Due:</span>
                    <span class="text-xl font-black {{ $reservation->balance_due > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                        BDT {{ number_format($reservation->balance_due, 2) }}
                    </span>
                </div>

                @if($reservation->latestInvoice)
                <div class="pt-2">
                    <a href="{{ route('billing.invoice.show', $reservation->latestInvoice) }}" class="w-full py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span>View Formal Tax Invoice</span>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Folio Items & Settlement Ledger -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Folio Items Ledger -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 font-heading">Guest Folio & Incidentals</h3>
                        <p class="text-xs text-slate-500">Itemized room charges, food, beverage, laundry, and services</p>
                    </div>

                    <!-- Add Folio Item Modal -->
                    <div x-data="{ folioModal: false }">
                        <button @click="folioModal = true" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition-colors flex items-center gap-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Post Service Charge</span>
                        </button>

                        <div x-show="folioModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                            <div @click.away="folioModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                                <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Post Service Charge to Folio</h3>
                                <p class="text-xs text-slate-500 mb-4">Add incidentals incurred during guest stay.</p>

                                <form method="POST" action="{{ route('reservations.folio.add', $reservation) }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Service Category</label>
                                        <select name="item_type" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                            <option value="restaurant">🍽️ Restaurant / Room Service</option>
                                            <option value="laundry">🧺 Laundry & Dry Cleaning</option>
                                            <option value="minibar">🍾 Mini Bar & Beverages</option>
                                            <option value="service">🚗 Airport Transfer / Concierge</option>
                                            <option value="damage">⚠️ Incident / Damage Fee</option>
                                            <option value="other">📌 Other Service</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Description</label>
                                        <input type="text" name="description" required placeholder="e.g. Dinner buffet 2 persons" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-medium">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Quantity</label>
                                            <input type="number" name="quantity" value="1" min="1" required class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Unit Price (BDT)</label>
                                            <input type="number" step="0.01" name="unit_price" required placeholder="0.00" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" @click="folioModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors">
                                            Add to Bill
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-5">Type</th>
                                <th class="py-3 px-4">Item Description</th>
                                <th class="py-3 px-4 text-center">Qty</th>
                                <th class="py-3 px-4 text-right">Unit Price</th>
                                <th class="py-3 px-4 text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($reservation->folioItems as $folio)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                        {{ str_replace('_', ' ', $folio->item_type) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $folio->description }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold text-slate-800">{{ $folio->quantity }}</td>
                                <td class="py-3.5 px-4 text-right text-slate-600">BDT {{ number_format($folio->unit_price, 2) }}</td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-slate-900">BDT {{ number_format($folio->total_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">No folio charges posted yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Records & Receipts -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 font-heading">Payments & Receipts</h3>
                        <p class="text-xs text-slate-500">Collected settlements against this reservation</p>
                    </div>

                    <!-- Collect Payment Modal -->
                    <div x-data="{ payModal: false }">
                        <button @click="payModal = true" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold shadow-xs transition-colors flex items-center gap-1">
                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                            <span>Collect Payment</span>
                        </button>

                        <div x-show="payModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                            <div @click.away="payModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                                <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Receive Guest Payment</h3>
                                <p class="text-xs text-slate-500 mb-4">Remaining balance due: BDT {{ number_format($reservation->balance_due, 2) }}</p>

                                <form method="POST" action="{{ route('billing.payment.record') }}" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                    @if($reservation->latestInvoice)
                                    <input type="hidden" name="invoice_id" value="{{ $reservation->latestInvoice->id }}">
                                    @endif

                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Amount to Collect (BDT)</label>
                                        <input type="number" step="0.01" name="amount" value="{{ $reservation->balance_due > 0 ? $reservation->balance_due : '' }}" required placeholder="0.00" class="w-full rounded-xl border border-slate-300 p-2.5 text-base font-bold text-emerald-700">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Payment Method</label>
                                        <select name="payment_method" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                            <option value="cash">💵 Cash</option>
                                            <option value="card">💳 Credit / Debit Card (POS)</option>
                                            <option value="bkash">📱 bKash Merchant</option>
                                            <option value="nagad">📱 Nagad Merchant</option>
                                            <option value="bank_transfer">🏦 Bank Wire / EFT</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Transaction Ref / Card Slip</label>
                                        <input type="text" name="transaction_reference" placeholder="e.g. POS-987654" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                                    </div>

                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" @click="payModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors">
                                            Process Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-5">Receipt #</th>
                                <th class="py-3 px-4">Amount</th>
                                <th class="py-3 px-4">Method</th>
                                <th class="py-3 px-4">Reference</th>
                                <th class="py-3 px-4">Received By</th>
                                <th class="py-3 px-4 text-center">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($reservation->payments as $p)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-3.5 px-5 font-mono font-bold text-slate-900">{{ $p->payment_number }}</td>
                                <td class="py-3.5 px-4 font-black text-emerald-700">BDT {{ number_format($p->amount, 2) }}</td>
                                <td class="py-3.5 px-4 uppercase text-[10px] font-bold text-slate-700">{{ $p->payment_method }}</td>
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">{{ $p->transaction_reference }}</td>
                                <td class="py-3.5 px-4 text-slate-700">{{ $p->receivedBy?->name ?? 'Front Desk' }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('billing.payment.receipt', $p) }}" target="_blank" class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">
                                        Print
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">No payments registered for this reservation yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
