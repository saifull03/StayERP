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
        guestId: '',
        firstName: '',
        lastName: '',
        phone: '',
        email: '',
        idType: 'nid',
        idNumber: '',
        searchResults: [],
        showSuggestions: false,
        activeField: '',
        isLinked: false,
        linkedGuestText: '',
        searching: false,
        searchTimeout: null,
        checkIn: '{{ now()->toDateString() }}',
        checkOut: '{{ now()->addDay()->toDateString() }}',
        selectedRoomRate: 0,
        nights: 1,

        searchGuests(query, field) {
            this.activeField = field;
            if (!query || query.trim().length < 2) {
                this.searchResults = [];
                this.showSuggestions = false;
                return;
            }

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.searching = true;
                fetch(`/guests/search?q=${encodeURIComponent(query.trim())}`)
                    .then(res => res.json())
                    .then(data => {
                        this.searchResults = data || [];
                        this.showSuggestions = this.searchResults.length > 0;
                        this.searching = false;
                    })
                    .catch(() => {
                        this.searchResults = [];
                        this.showSuggestions = false;
                        this.searching = false;
                    });
            }, 180);
        },

        selectGuest(g) {
            this.guestId = g.id;
            this.firstName = g.first_name || '';
            this.lastName = g.last_name || '';
            this.phone = g.phone || '';
            this.email = g.email || '';
            this.idType = g.id_type || 'nid';
            this.idNumber = g.id_number || '';
            this.isLinked = true;
            this.linkedGuestText = `${g.first_name} ${g.last_name || ''} (${g.phone})`.trim();
            this.showSuggestions = false;
            this.searchResults = [];
        },

        unlinkGuest() {
            this.guestId = '';
            this.isLinked = false;
            this.linkedGuestText = '';
        },

        loadFromDirectory(e) {
            const val = e.target.value;
            if (!val) {
                this.unlinkGuest();
                return;
            }
            fetch(`/guests/search?q=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    const found = data.find(item => item.id == val) || data[0];
                    if (found) {
                        this.selectGuest(found);
                    }
                });
        },

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
        <input type="hidden" name="existing_guest_id" :value="guestId">

        <!-- 1. Guest Information Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-heading">1. Guest Profile</h3>
                    <p class="text-[11px] text-slate-500">Search existing records instantly or enter new guest details</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <label class="flex items-center gap-1.5 cursor-pointer font-medium">
                        <input type="radio" name="guest_type" value="new" x-model="guestType" class="text-emerald-600 focus:ring-emerald-500">
                        <span>New / Search Guest</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer font-medium">
                        <input type="radio" name="guest_type" value="existing" x-model="guestType" class="text-emerald-600 focus:ring-emerald-500">
                        <span>Existing Guest Directory</span>
                    </label>
                </div>
            </div>

            <!-- Linked Guest Banner -->
            <div x-show="isLinked" x-cloak class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs transition-all">
                <div class="flex items-center gap-2 text-emerald-900 font-semibold">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-600 text-white text-[11px] font-bold">&check;</span>
                    <span>Existing Guest Linked: <strong class="text-emerald-700" x-text="linkedGuestText"></strong></span>
                </div>
                <button type="button" @click="unlinkGuest()" class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline">
                    Unlink (Register as New)
                </button>
            </div>

            <!-- Existing Guest Select Directory -->
            <div x-show="guestType === 'existing'" x-cloak>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Select Registered Guest from Directory</label>
                <select name="guest_id" @change="loadFromDirectory($event)" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    <option value="">-- Choose guest from directory --</option>
                    @foreach($guests as $g)
                        <option value="{{ $g->id }}" :selected="guestId == {{ $g->id }}">{{ $g->full_name }} ({{ $g->phone }}) &bull; {{ $g->email }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Guest Input Fields (with Instant Search Popovers) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- First Name -->
                <div class="relative" @click.away="if(activeField === 'name') showSuggestions = false">
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" x-model="firstName" 
                           @input="searchGuests(firstName, 'name'); if(isLinked && !guestId) unlinkGuest();"
                           placeholder="e.g. Mahmudul" 
                           autocomplete="off"
                           required
                           class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">

                    <!-- Suggestions Dropdown for Name -->
                    <div x-show="showSuggestions && activeField === 'name' && searchResults.length > 0" 
                         x-cloak 
                         class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100">
                        <div class="p-2 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Matching Registered Guests</span>
                            <span class="text-[10px] lowercase text-slate-400">click to auto-fill</span>
                        </div>
                        <template x-for="g in searchResults" :key="g.id">
                            <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                <div>
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span x-text="g.first_name + ' ' + (g.last_name || '')"></span>
                                        <template x-if="g.vip_status">
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-800">VIP</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        <span class="font-semibold text-emerald-700" x-text="g.phone"></span> &bull; 
                                        <span x-text="g.email || 'No email'"></span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded shrink-0">
                                    Auto-fill &rarr;
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" x-model="lastName" placeholder="e.g. Hasan" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                </div>

                <!-- Phone Number (Instant Search Hub) -->
                <div class="relative" @click.away="if(activeField === 'phone') showSuggestions = false">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase text-slate-700">Phone Number *</label>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Instant Search</span>
                    </div>
                    <div class="relative">
                        <input type="text" name="phone" x-model="phone" 
                               @input="searchGuests(phone, 'phone'); if(isLinked && !guestId) unlinkGuest();"
                               @focus="if(phone && phone.length >= 2) searchGuests(phone, 'phone')"
                               placeholder="+880 1711 000000" 
                               autocomplete="off"
                               required
                               class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                        
                        <div x-show="searching && activeField === 'phone'" class="absolute right-3 top-2.5">
                            <span class="inline-block w-3.5 h-3.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                    </div>

                    <!-- Suggestions Dropdown for Phone -->
                    <div x-show="showSuggestions && activeField === 'phone' && searchResults.length > 0" 
                         x-cloak 
                         class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100">
                        <div class="p-2 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Matching Registered Guests</span>
                            <span class="text-[10px] lowercase text-slate-400">click to auto-fill</span>
                        </div>
                        <template x-for="g in searchResults" :key="g.id">
                            <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                <div>
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span x-text="g.first_name + ' ' + (g.last_name || '')"></span>
                                        <template x-if="g.vip_status">
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-800">VIP</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        <span class="font-bold text-emerald-700" x-text="g.phone"></span> &bull; 
                                        <span x-text="g.email || 'No email'"></span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded shrink-0">
                                    Auto-fill &rarr;
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Email Address (Instant Search Hub) -->
                <div class="relative" @click.away="if(activeField === 'email') showSuggestions = false">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase text-slate-700">Email Address</label>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Instant Search</span>
                    </div>
                    <div class="relative">
                        <input type="email" name="email" x-model="email" 
                               @input="searchGuests(email, 'email'); if(isLinked && !guestId) unlinkGuest();"
                               @focus="if(email && email.length >= 2) searchGuests(email, 'email')"
                               placeholder="guest@example.com" 
                               autocomplete="off"
                               class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">

                        <div x-show="searching && activeField === 'email'" class="absolute right-3 top-2.5">
                            <span class="inline-block w-3.5 h-3.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                    </div>

                    <!-- Suggestions Dropdown for Email -->
                    <div x-show="showSuggestions && activeField === 'email' && searchResults.length > 0" 
                         x-cloak 
                         class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100">
                        <div class="p-2 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Matching Registered Guests</span>
                            <span class="text-[10px] lowercase text-slate-400">click to auto-fill</span>
                        </div>
                        <template x-for="g in searchResults" :key="g.id">
                            <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                <div>
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span x-text="g.first_name + ' ' + (g.last_name || '')"></span>
                                        <template x-if="g.vip_status">
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-800">VIP</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        <span class="font-bold text-emerald-700" x-text="g.email || g.phone"></span> &bull; 
                                        <span x-text="g.phone"></span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded shrink-0">
                                    Auto-fill &rarr;
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Identity Type -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Identity Type</label>
                    <select name="id_type" x-model="idType" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                        <option value="nid">National ID Card (NID)</option>
                        <option value="passport">International Passport</option>
                        <option value="driving_license">Driving License</option>
                        <option value="other">Other Official ID</option>
                    </select>
                </div>

                <!-- ID Number (Instant Search Hub) -->
                <div class="relative" @click.away="if(activeField === 'id_number') showSuggestions = false">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase text-slate-700">ID / Passport Number</label>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Instant Search</span>
                    </div>
                    <div class="relative">
                        <input type="text" name="id_number" x-model="idNumber" 
                               @input="searchGuests(idNumber, 'id_number'); if(isLinked && !guestId) unlinkGuest();"
                               @focus="if(idNumber && idNumber.length >= 2) searchGuests(idNumber, 'id_number')"
                               placeholder="ID or Passport Number" 
                               autocomplete="off"
                               class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">

                        <div x-show="searching && activeField === 'id_number'" class="absolute right-3 top-2.5">
                            <span class="inline-block w-3.5 h-3.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                    </div>

                    <!-- Suggestions Dropdown for ID Number -->
                    <div x-show="showSuggestions && activeField === 'id_number' && searchResults.length > 0" 
                         x-cloak 
                         class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100">
                        <div class="p-2 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Matching Guests by ID/Passport</span>
                            <span class="text-[10px] lowercase text-slate-400">click to auto-fill</span>
                        </div>
                        <template x-for="g in searchResults" :key="g.id">
                            <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                <div>
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span x-text="g.first_name + ' ' + (g.last_name || '')"></span>
                                        <template x-if="g.vip_status">
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-800">VIP</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        <span class="font-bold text-emerald-700" x-text="(g.id_type ? g.id_type.toUpperCase() + ': ' : '') + (g.id_number || 'N/A')"></span> &bull; 
                                        <span x-text="g.phone"></span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded shrink-0">
                                    Auto-fill &rarr;
                                </span>
                            </button>
                        </template>
                    </div>
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
