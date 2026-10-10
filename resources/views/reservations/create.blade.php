@extends('layouts.app')

@section('title', 'New Reservation')
@section('page-header', 'Book New Reservation')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('reservations.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Reservations</span>
        </a>
        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
            Branch: {{ $activeBranch->name }}
        </span>
    </div>

    <form method="POST" action="{{ route('reservations.store') }}" class="space-y-6" x-data="{
        guestType: 'new',
        checkIn: '{{ now()->toDateString() }}',
        checkOut: '{{ now()->addDay()->toDateString() }}',
        selectedRoomRate: 0,
        nights: 1,
        updateRate(elem) {
            const opt = elem.options[elem.selectedIndex];
            this.selectedRoomRate = opt ? parseFloat(opt.getAttribute('data-price') || 0) : 0;
            this.calculateNights();
        },
        calculateNights() {
            const inDate = new Date(this.checkIn);
            const outDate = new Date(this.checkOut);
            const diffTime = outDate - inDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            this.nights = diffDays > 0 ? diffDays : 1;
        }
    }">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $activeBranch->id }}">

        <!-- 1. Guest Information Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900 font-heading">1. Guest Profile</h3>
                <div class="flex items-center gap-3 text-xs">
                    <label class="flex items-center gap-1.5 cursor-pointer font-medium">
                        <input type="radio" name="guest_type" value="new" x-model="guestType" class="text-emerald-600 focus:ring-emerald-500">
                        <span>New Guest</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer font-medium">
                        <input type="radio" name="guest_type" value="existing" x-model="guestType" class="text-emerald-600 focus:ring-emerald-500">
                        <span>Existing Guest Directory</span>
                    </label>
                </div>
            </div>

            <!-- Existing Guest Select -->
            <div x-show="guestType === 'existing'" x-cloak>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Select Registered Guest</label>
                <select name="guest_id" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    <option value="">-- Choose guest from directory --</option>
                    @foreach($guests as $g)
                        <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->phone }}) &bull; {{ $g->email }}</option>
                    @endforeach
                </select>
            </div>

            <!-- New Guest Inputs -->
            <div x-show="guestType === 'new'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" placeholder="e.g. Mahmudul" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" placeholder="e.g. Hasan" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Phone Number *</label>
                    <input type="text" name="phone" placeholder="+880 1711 000000" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="guest@example.com" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Identity Type</label>
                    <select name="id_type" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                        <option value="nid">National ID Card (NID)</option>
                        <option value="passport">International Passport</option>
                        <option value="driving_license">Driving License</option>
                        <option value="other">Other Official ID</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">ID Number</label>
                    <input type="text" name="id_number" placeholder="ID or Passport Number" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
            </div>
        </div>

        <!-- 2. Stay Dates & Room Assignment -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 font-heading border-b border-slate-100 pb-3">2. Stay Dates & Physical Room Assignment</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Check-In Date *</label>
                    <input type="date" name="check_in_date" x-model="checkIn" @change="calculateNights()" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Check-Out Date *</label>
                    <input type="date" name="check_out_date" x-model="checkOut" @change="calculateNights()" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Assign Physical Room *</label>
                <select name="room_id" @change="updateRate($event.target)" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold text-slate-800 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    <option value="">-- Choose available room --</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" data-price="{{ $r->roomType->base_price_per_night }}" {{ $r->operational_status !== 'available' ? 'disabled' : '' }}>
                            Room {{ $r->room_number }} (Floor {{ $r->floor }}) &bull; {{ $r->roomType->name }} &bull; BDT {{ number_format($r->roomType->base_price_per_night, 2) }}/night
                            @if($r->operational_status !== 'available') [Status: {{ $r->operational_status }}] @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Adults Count</label>
                    <input type="number" name="adults" value="1" min="1" max="10" required class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Children Count</label>
                    <input type="number" name="children" value="0" min="0" max="6" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Booking Channel</label>
                    <select name="source" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                        <option value="walk_in">Walk-In Front Desk</option>
                        <option value="direct">Direct Phone / Email</option>
                        <option value="online">Online Web Portal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Special Requests / Preferences</label>
                <textarea name="special_requests" rows="2" placeholder="e.g. Non-smoking room, extra pillows, early check-in" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs"></textarea>
            </div>
        </div>

        <!-- 3. Billing & Advance Settlement -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 font-heading border-b border-slate-100 pb-3">3. Billing Estimation & Advance Deposit</h3>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-600">Calculated Stay Duration:</span>
                    <span class="font-bold text-slate-900"><span x-text="nights"></span> Night(s)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Room Base Charge:</span>
                    <span class="font-bold text-slate-900">BDT <span x-text="(selectedRoomRate * nights).toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2 font-bold text-sm text-slate-900">
                    <span>Estimated Total (Excl. Tax):</span>
                    <span>BDT <span x-text="(selectedRoomRate * nights).toFixed(2)"></span></span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Advance Deposit Amount (BDT)</label>
                    <input type="number" step="0.01" name="advance_payment" placeholder="0.00" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold text-emerald-700">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Deposit Payment Method</label>
                    <select name="payment_method" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Credit / Debit Card</option>
                        <option value="bkash">📱 bKash</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('reservations.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-colors">
                    Confirm & Create Reservation
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
