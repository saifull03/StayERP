@extends('layouts.app')

@section('title', 'Front Desk Walk-In POS Terminal')
@section('page-header', 'Walk-In & Front Desk POS')

@section('content')
@php
    $roomsJson = $rooms->map(function($r) {
        return [
            'id' => $r->id,
            'room_number' => $r->room_number,
            'floor' => (string) $r->floor,
            'operational_status' => $r->operational_status,
            'type_id' => $r->room_type_id,
            'type_name' => $r->roomType->name ?? 'Standard Room',
            'price' => (float) ($r->roomType->base_price_per_night ?? 0),
            'max_adults' => $r->roomType->max_adults ?? 2,
            'max_children' => $r->roomType->max_children ?? 1,
            'size_sqft' => $r->roomType->size_sqft ?? 250,
            'is_available' => $r->operational_status === 'available',
        ];
    });
@endphp

<div class="space-y-6 max-w-7xl mx-auto" x-data="{
    // Mode
    mode: 'walk_in',

    // Guest Profile
    guestType: 'new',
    guestId: '',
    firstName: '',
    lastName: '',
    phone: '',
    email: '',
    idType: 'nid',
    idNumber: '',
    isLinked: false,
    linkedGuest: null,
    linkedGuestText: '',

    // Universal Fast Search
    universalSearch: '',
    universalSearching: false,
    universalResults: [],
    universalTimeout: null,

    // Field-level search
    searchResults: [],
    showSuggestions: false,
    activeField: '',
    searching: false,
    searchTimeout: null,

    // Rooms & Grid
    allRooms: {{ Js::from($roomsJson) }},
    roomTypeFilter: 'all',
    floorFilter: 'all',
    onlyAvailable: true,
    selectedRoomId: '',
    selectedRoom: null,
    selectedRoomRate: 0,

    // Stay Dates
    checkIn: '{{ now()->toDateString() }}',
    checkOut: '{{ now()->addDay()->toDateString() }}',
    nights: 1,
    adults: 1,
    children: 0,
    source: 'walk_in',
    specialRequests: '',

    // Billing & Tender
    advancePayment: '',
    paymentMethod: 'cash',
    isSubmitting: false,

    init() {
        this.calculateNights();
        // If there are rooms, pre-select the first available one for instant POS speed
        const firstAvail = this.allRooms.find(r => r.is_available);
        if (firstAvail) {
            this.selectRoom(firstAvail);
        }
    },

    // Room Selection
    selectRoom(room) {
        if (!room.is_available) return;
        this.selectedRoomId = room.id;
        this.selectedRoom = room;
        this.selectedRoomRate = room.price;
        this.calculateTotals();
    },

    get filteredRooms() {
        return this.allRooms.filter(r => {
            if (this.onlyAvailable && !r.is_available) return false;
            if (this.roomTypeFilter !== 'all' && r.type_id != this.roomTypeFilter) return false;
            if (this.floorFilter !== 'all' && r.floor != this.floorFilter) return false;
            return true;
        });
    },

    get availableCount() {
        return this.allRooms.filter(r => r.is_available).length;
    },

    // Dates & Nights
    setNights(n) {
        this.nights = n;
        if (!this.checkIn) {
            this.checkIn = new Date().toISOString().split('T')[0];
        }
        const d = new Date(this.checkIn);
        d.setDate(d.getDate() + n);
        this.checkOut = d.toISOString().split('T')[0];
        this.calculateTotals();
    },

    calculateNights() {
        if (this.checkIn && this.checkOut) {
            const inDate = new Date(this.checkIn);
            const outDate = new Date(this.checkOut);
            const diffTime = outDate - inDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            this.nights = diffDays > 0 ? diffDays : 1;
        }
        this.calculateTotals();
    },

    // Universal Fast Search
    onUniversalSearch() {
        const q = (this.universalSearch || '').trim();
        if (q.length < 2) {
            this.universalResults = [];
            this.universalSearching = false;
            return;
        }
        clearTimeout(this.universalTimeout);
        this.universalSearching = true;
        this.universalTimeout = setTimeout(() => {
            fetch(`/guests/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    this.universalResults = data || [];
                    this.universalSearching = false;
                })
                .catch(() => {
                    this.universalResults = [];
                    this.universalSearching = false;
                });
        }, 150);
    },

    // Field-level search
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
        this.linkedGuest = g;
        this.linkedGuestText = `${g.first_name} ${g.last_name || ''} (${g.phone})`.trim();
        this.showSuggestions = false;
        this.searchResults = [];
        this.universalResults = [];
        this.universalSearch = '';
    },

    unlinkGuest() {
        this.guestId = '';
        this.isLinked = false;
        this.linkedGuest = null;
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

    // Totals & Calculations
    get subtotal() {
        return (this.selectedRoomRate || 0) * (this.nights || 1);
    },

    get estimatedTax() {
        return Math.round(this.subtotal * 0.15 * 100) / 100;
    },

    get grandTotal() {
        return Math.round((this.subtotal + this.estimatedTax) * 100) / 100;
    },

    calculateTotals() {
        // Automatically suggestions can be recalculated
    },

    // Quick tender amounts
    setQuickTender(amount) {
        this.advancePayment = amount.toFixed(2);
    },

    // Special Request Tag insertion
    addRequestTag(tag) {
        if (!this.specialRequests) {
            this.specialRequests = tag;
        } else if (!this.specialRequests.includes(tag)) {
            this.specialRequests += ', ' + tag;
        }
    }
}">

    <!-- Top POS Terminal Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-5 rounded-2xl text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4 border border-slate-800">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 shadow-inner">
                <i data-lucide="store" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black font-heading tracking-tight">Front Desk Walk-In POS Terminal</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Desk
                    </span>
                </div>
                <p class="text-xs text-slate-300 mt-0.5 flex items-center gap-2">
                    <span>Active Branch: <strong class="text-white">{{ $activeBranch->name }}</strong> ({{ $activeBranch->code }})</span>
                    <span>&bull;</span>
                    <span class="text-emerald-300 font-semibold"><span x-text="availableCount"></span> Rooms Ready</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="bg-slate-800/80 border border-slate-700 rounded-xl p-1 flex items-center text-xs">
                <button type="button" 
                        @click="mode = 'walk_in'; source = 'walk_in'; setNights(1)" 
                        :class="mode === 'walk_in' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                    <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                    <span>Fast Walk-In</span>
                </button>
                <button type="button" 
                        @click="mode = 'advance'; source = 'direct'" 
                        :class="mode === 'advance' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                    <span>Advance Booking</span>
                </button>
            </div>
            <a href="{{ route('reservations.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-semibold border border-slate-700 transition-colors flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Exit Desk</span>
            </a>
        </div>
    </div>

    <!-- Main POS Form Container -->
    <form method="POST" action="{{ route('reservations.store') }}" @submit="isSubmitting = true" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $activeBranch->id }}">
        <input type="hidden" name="existing_guest_id" :value="guestId">
        <input type="hidden" name="room_id" :value="selectedRoomId" required>

        <!-- LEFT WORKSPACE (8 Columns on Large Screens) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-6">

            <!-- 1. GUEST IDENTITY & POS SCANNER HUB -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-xs">
                            1
                        </div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading">Guest Identification</h3>
                            <p class="text-[11px] text-slate-500">Scan or search existing guest profile or enter walk-in guest details</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer font-medium text-slate-700">
                            <input type="radio" name="guest_type" value="new" x-model="guestType" class="text-emerald-600 focus:ring-emerald-500">
                            <span>Walk-In / Search</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-medium text-slate-700">
                            <input type="radio" name="guest_type" value="existing" x-model="guestType" class="text-emerald-600 focus:ring-emerald-500">
                            <span>Directory</span>
                        </label>
                    </div>
                </div>

                <!-- Universal POS Fast-Lookup Scanner Bar -->
                <div class="relative">
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="scan-barcode" class="w-3.5 h-3.5 text-emerald-600"></i>
                            <span>Fast Guest Scanner & Instant Search</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-normal">Search by Phone, NID / Passport, Email, or Name</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               x-model="universalSearch" 
                               @input="onUniversalSearch()" 
                               placeholder="🔍 Scan ID or type phone, email, NID/Passport (e.g. 017..., 1990269..., Mahmudul)..."
                               class="w-full pl-10 pr-10 py-3 rounded-xl border-2 border-emerald-200 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 text-xs font-semibold bg-emerald-50/20 text-slate-900 outline-hidden transition-all shadow-xs">
                        <i data-lucide="search" class="w-4 h-4 text-emerald-600 absolute left-3.5 top-3.5"></i>
                        
                        <div x-show="universalSearching" class="absolute right-3.5 top-3.5">
                            <span class="inline-block w-4 h-4 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                        <button type="button" x-show="universalSearch && !universalSearching" @click="universalSearch = ''; universalResults = []" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Universal Search Instant Match Drawer -->
                    <div x-show="universalResults.length > 0" 
                         x-cloak 
                         class="absolute left-0 right-0 mt-2 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 max-h-72 overflow-y-auto divide-y divide-slate-100">
                        <div class="p-2.5 bg-slate-900 text-white text-[11px] font-bold flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-400"></i>
                                <span>Found <strong class="text-emerald-400" x-text="universalResults.length"></strong> Matching Guests</span>
                            </span>
                            <span class="text-[10px] text-slate-300 font-normal">Tap guest card to auto-fill</span>
                        </div>
                        <template x-for="g in universalResults" :key="g.id">
                            <button type="button" @click="selectGuest(g)" class="w-full text-left p-3 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0">
                                        <span x-text="g.first_name ? g.first_name.charAt(0) : 'G'"></span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span x-text="g.first_name + ' ' + (g.last_name || '')"></span>
                                            <template x-if="g.vip_status">
                                                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-800">VIP</span>
                                            </template>
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                            <span class="font-bold text-emerald-700" x-text="g.phone"></span>
                                            <span>&bull;</span>
                                            <span x-text="(g.id_type ? g.id_type.toUpperCase() + ': ' : '') + (g.id_number || 'No ID')"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[11px] shadow-xs shrink-0 flex items-center gap-1">
                                    <span>Select Guest</span>
                                    <span>&rarr;</span>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Linked Guest Status Card -->
                <div x-show="isLinked" x-cloak class="p-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-300 rounded-xl flex items-center justify-between text-xs transition-all shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                            &check;
                        </div>
                        <div>
                            <div class="font-bold text-emerald-950 flex items-center gap-1.5">
                                <span>Existing Guest Linked:</span>
                                <strong class="text-emerald-800 font-black text-sm" x-text="linkedGuestText"></strong>
                                <template x-if="linkedGuest && linkedGuest.vip_status">
                                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase bg-amber-200 text-amber-900">VIP</span>
                                </template>
                            </div>
                            <div class="text-[11px] text-emerald-700 mt-0.5">
                                <span x-text="idType ? idType.toUpperCase() + ': ' + (idNumber || 'N/A') : ''"></span> &bull; 
                                <span x-text="email || 'No email registered'"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="unlinkGuest()" class="px-3 py-1.5 bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1">
                        <i data-lucide="user-x" class="w-3.5 h-3.5"></i>
                        <span>Unlink (New Guest)</span>
                    </button>
                </div>

                <!-- Directory Select Alternative -->
                <div x-show="guestType === 'existing'" x-cloak>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Select from Registered Directory</label>
                    <select name="guest_id" @change="loadFromDirectory($event)" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 bg-white">
                        <option value="">-- Choose guest from directory --</option>
                        @foreach($guests as $g)
                            <option value="{{ $g->id }}" :selected="guestId == {{ $g->id }}">{{ $g->full_name }} ({{ $g->phone }}) &bull; {{ $g->email ?? 'No email' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Guest Input Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div class="relative" @click.away="if(activeField === 'name') showSuggestions = false">
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" x-model="firstName" 
                               @input="searchGuests(firstName, 'name'); if(isLinked && !guestId) unlinkGuest();"
                               placeholder="e.g. Mahmudul" 
                               autocomplete="off"
                               required
                               class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">

                        <!-- Suggestions Dropdown for Name -->
                        <div x-show="showSuggestions && activeField === 'name' && searchResults.length > 0" 
                             x-cloak 
                             class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                            <template x-for="g in searchResults" :key="g.id">
                                <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-900" x-text="g.first_name + ' ' + (g.last_name || '')"></div>
                                        <div class="text-[11px] text-slate-500" x-text="g.phone + ' • ' + (g.email || '')"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Auto-fill &rarr;</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" x-model="lastName" placeholder="e.g. Hasan" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
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
                             class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                            <template x-for="g in searchResults" :key="g.id">
                                <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-900" x-text="g.first_name + ' ' + (g.last_name || '')"></div>
                                        <div class="text-[11px] text-emerald-700 font-bold" x-text="g.phone"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Auto-fill &rarr;</span>
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
                                   class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">

                            <div x-show="searching && activeField === 'email'" class="absolute right-3 top-2.5">
                                <span class="inline-block w-3.5 h-3.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                            </div>
                        </div>

                        <!-- Suggestions Dropdown for Email -->
                        <div x-show="showSuggestions && activeField === 'email' && searchResults.length > 0" 
                             x-cloak 
                             class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                            <template x-for="g in searchResults" :key="g.id">
                                <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-900" x-text="g.first_name + ' ' + (g.last_name || '')"></div>
                                        <div class="text-[11px] text-emerald-700 font-bold" x-text="g.email || g.phone"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Auto-fill &rarr;</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Identity Type -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Identity Type</label>
                        <select name="id_type" x-model="idType" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 bg-white">
                            <option value="nid">National ID Card (NID)</option>
                            <option value="passport">International Passport</option>
                            <option value="driving_license">Driving License</option>
                            <option value="other">Other Official ID</option>
                        </select>
                    </div>

                    <!-- ID / Passport Number (Instant Search Hub) -->
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
                             class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                            <template x-for="g in searchResults" :key="g.id">
                                <button type="button" @click="selectGuest(g)" class="w-full text-left p-2.5 hover:bg-emerald-50 transition-colors flex items-center justify-between gap-2 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-900" x-text="g.first_name + ' ' + (g.last_name || '')"></div>
                                        <div class="text-[11px] text-emerald-700 font-bold" x-text="(g.id_type ? g.id_type.toUpperCase() + ': ' : '') + (g.id_number || 'N/A')"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Auto-fill &rarr;</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. VISUAL INTERACTIVE ROOM SELECTOR (POS TILE MATRIX) -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-xs">
                            2
                        </div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading">Interactive Room Matrix</h3>
                            <p class="text-[11px] text-slate-500">Tap any available room tile to assign instantly</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                            <input type="checkbox" x-model="onlyAvailable" class="rounded text-emerald-600 focus:ring-emerald-500">
                            <span>Available Only (<span x-text="availableCount"></span>)</span>
                        </label>
                    </div>
                </div>

                <!-- Room Filter Pills -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="text-[11px] font-bold uppercase text-slate-400 mr-1">Types:</span>
                    <button type="button" 
                            @click="roomTypeFilter = 'all'" 
                            :class="roomTypeFilter === 'all' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-2.5 py-1 rounded-lg text-xs transition-colors">
                        All Types
                    </button>
                    @foreach($roomTypes as $type)
                    <button type="button" 
                            @click="roomTypeFilter = {{ $type->id }}" 
                            :class="roomTypeFilter == {{ $type->id }} ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-2.5 py-1 rounded-lg text-xs transition-colors">
                        {{ $type->name }}
                    </button>
                    @endforeach
                </div>

                <!-- Visual Room Tile Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 max-h-96 overflow-y-auto p-1">
                    <template x-for="r in filteredRooms" :key="r.id">
                        <div @click="selectRoom(r)"
                             :class="{
                                 'border-emerald-600 ring-2 ring-emerald-600 bg-emerald-50/50 shadow-md': selectedRoomId == r.id,
                                 'border-slate-200 bg-white hover:border-emerald-400 hover:shadow-xs': selectedRoomId != r.id && r.is_available,
                                 'border-slate-100 bg-slate-50 opacity-60 cursor-not-allowed': !r.is_available
                             }"
                             class="relative p-3.5 rounded-xl border-2 cursor-pointer transition-all flex flex-col justify-between select-none">
                            
                            <!-- Top Tile Header -->
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-base font-black text-slate-900 tracking-tight font-heading" x-text="'Room ' + r.room_number"></span>
                                    <div class="text-[10px] text-slate-500 font-medium" x-text="'Floor ' + r.floor + ' • ' + r.size_sqft + ' sqft'"></div>
                                </div>
                                
                                <!-- Status Dot / Pill -->
                                <div>
                                    <template x-if="r.is_available">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                            Ready
                                        </span>
                                    </template>
                                    <template x-if="!r.is_available">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-200 text-slate-700" x-text="r.operational_status">
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Room Type & Rate -->
                            <div class="mt-2.5 pt-2 border-t border-slate-100">
                                <div class="text-[11px] font-semibold text-slate-700 truncate" x-text="r.type_name"></div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs font-black text-emerald-700" x-text="'BDT ' + r.price.toLocaleString()"></span>
                                    <span class="text-[10px] text-slate-400">/ night</span>
                                </div>
                            </div>

                            <!-- Selected Checkmark Badge -->
                            <div x-show="selectedRoomId == r.id" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-emerald-600 text-white rounded-full flex items-center justify-center text-[10px] font-bold shadow-xs">
                                &check;
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Fallback Traditional Select (Synchronized) -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span class="text-slate-500">Currently Selected:</span>
                    <strong class="font-bold text-slate-900" x-text="selectedRoom ? ('Room ' + selectedRoom.room_number + ' • ' + selectedRoom.type_name + ' • BDT ' + selectedRoom.price.toFixed(2) + '/night') : 'No room selected yet'"></strong>
                </div>
            </div>

            <!-- 3. STAY DATES & PREFERENCES (TOUCH CONTROLS) -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-xs">
                        3
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading">Stay Duration & Preferences</h3>
                        <p class="text-[11px] text-slate-500">Fast duration buttons and guest count steppers</p>
                    </div>
                </div>

                <!-- Quick Stay Duration Buttons -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Quick Stay Presets</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="setNights(1)" 
                                :class="nights === 1 ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                            ⚡ 1 Night (Walk-In)
                        </button>
                        <button type="button" @click="setNights(2)" 
                                :class="nights === 2 ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                            2 Nights (Weekend)
                        </button>
                        <button type="button" @click="setNights(3)" 
                                :class="nights === 3 ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                            3 Nights
                        </button>
                        <button type="button" @click="setNights(5)" 
                                :class="nights === 5 ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                            5 Nights
                        </button>
                        <button type="button" @click="setNights(7)" 
                                :class="nights === 7 ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                            7 Nights (Weekly)
                        </button>
                    </div>
                </div>

                <!-- Date Pickers -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Check-In Date *</label>
                        <input type="date" name="check_in_date" x-model="checkIn" @change="calculateNights()" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Check-Out Date *</label>
                        <input type="date" name="check_out_date" x-model="checkOut" @change="calculateNights()" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 bg-white">
                    </div>
                </div>

                <!-- Steppers: Adults, Children, Booking Channel -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Adults Stepper -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Adults Count</label>
                        <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden bg-white">
                            <button type="button" @click="if(adults > 1) adults--" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">-</button>
                            <input type="number" name="adults" x-model="adults" min="1" max="10" required class="w-full text-center p-2 text-xs font-bold border-none outline-hidden">
                            <button type="button" @click="if(adults < 10) adults++" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">+</button>
                        </div>
                    </div>

                    <!-- Children Stepper -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Children Count</label>
                        <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden bg-white">
                            <button type="button" @click="if(children > 0) children--" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">-</button>
                            <input type="number" name="children" x-model="children" min="0" max="6" class="w-full text-center p-2 text-xs font-bold border-none outline-hidden">
                            <button type="button" @click="if(children < 6) children++" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">+</button>
                        </div>
                    </div>

                    <!-- Booking Channel -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Booking Channel</label>
                        <select name="source" x-model="source" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 bg-white">
                            <option value="walk_in">🚶 Walk-In Front Desk</option>
                            <option value="direct">📞 Direct Phone / Desk</option>
                            <option value="online">🌐 Online Web Portal</option>
                        </select>
                    </div>
                </div>

                <!-- Special Requests with Quick Click Chips -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Special Requests / Preferences</label>
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        <button type="button" @click="addRequestTag('Non-smoking room')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-600 text-[11px] font-medium transition-colors">
                            + Non-Smoking
                        </button>
                        <button type="button" @click="addRequestTag('High floor view')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-600 text-[11px] font-medium transition-colors">
                            + High Floor
                        </button>
                        <button type="button" @click="addRequestTag('Quiet corner room')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-600 text-[11px] font-medium transition-colors">
                            + Quiet Corner
                        </button>
                        <button type="button" @click="addRequestTag('Extra towels & pillows')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-600 text-[11px] font-medium transition-colors">
                            + Extra Towels
                        </button>
                        <button type="button" @click="addRequestTag('Late check-out requested')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-600 text-[11px] font-medium transition-colors">
                            + Late Check-out
                        </button>
                    </div>
                    <textarea name="special_requests" x-model="specialRequests" rows="2" placeholder="e.g. Non-smoking room, extra pillows, early check-in" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"></textarea>
                </div>
            </div>

        </div>

        <!-- RIGHT POS REGISTER TICKET & TENDER (4-5 Columns on Large Screens) -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-6 space-y-4">
            
            <!-- Live POS Register Folio Ticket -->
            <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-2xl border border-slate-800 relative overflow-hidden">
                <!-- Background Accent Glow -->
                <div class="absolute -right-16 -top-16 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <!-- Ticket Header -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="receipt" class="w-5 h-5 text-emerald-400"></i>
                        <h4 class="font-black text-sm uppercase tracking-wider font-heading">Stay POS Register</h4>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400 bg-slate-800 px-2 py-0.5 rounded">TICKET #{{ strtoupper(Str::random(6)) }}</span>
                </div>

                <!-- Guest & Stay Snippet -->
                <div class="py-3.5 border-b border-slate-800/80 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Guest:</span>
                        <span class="font-bold text-white text-right truncate max-w-44" x-text="firstName ? (firstName + ' ' + (lastName || '')) : 'Walk-In Guest (Unsaved)'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Room Assignment:</span>
                        <span class="font-bold text-emerald-400" x-text="selectedRoom ? ('Room ' + selectedRoom.room_number + ' (' + selectedRoom.type_name + ')') : 'None Selected'"></span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-400">Dates:</span>
                        <span class="text-slate-300" x-text="checkIn + ' &rarr; ' + checkOut + ' (' + nights + ' nt)'"></span>
                    </div>
                </div>

                <!-- Itemized Cost Calculations -->
                <div class="py-3.5 space-y-2 text-xs border-b border-dashed border-slate-700">
                    <div class="flex justify-between text-slate-300">
                        <span>Nightly Base Rate:</span>
                        <span class="font-mono">BDT <span x-text="selectedRoomRate.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <span>Duration (<span x-text="nights"></span> Nights):</span>
                        <span class="font-mono">BDT <span x-text="subtotal.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-slate-400 text-[11px]">
                        <span>Estimated Taxes (15% VAT):</span>
                        <span class="font-mono">BDT <span x-text="estimatedTax.toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Grand Total Display -->
                <div class="py-4">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 mb-0.5">Estimated Grand Total (Due)</div>
                    <div class="text-3xl font-black text-emerald-400 tracking-tight font-heading font-mono flex items-baseline gap-1">
                        <span class="text-lg">BDT</span>
                        <span x-text="grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                    </div>
                </div>

                <!-- Advance Deposit / Tender -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase text-slate-300">Advance Deposit Tender</label>
                        <span class="text-[10px] text-emerald-400 font-semibold">Instant settlement</span>
                    </div>

                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">BDT</span>
                        <input type="number" step="0.01" name="advance_payment" x-model="advancePayment" placeholder="0.00" class="w-full bg-slate-800 border border-slate-700 rounded-xl pl-11 pr-3 py-2 text-xs font-bold text-emerald-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-hidden font-mono">
                    </div>

                    <!-- Quick Cash Tender Keys -->
                    <div class="grid grid-cols-3 gap-1.5 pt-1">
                        <button type="button" @click="setQuickTender(grandTotal)" class="px-2 py-1.5 bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 text-slate-300 text-[11px] font-bold rounded-lg border border-slate-700 transition-colors">
                            Exact Full
                        </button>
                        <button type="button" @click="setQuickTender(grandTotal / 2)" class="px-2 py-1.5 bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 text-slate-300 text-[11px] font-bold rounded-lg border border-slate-700 transition-colors">
                            50% Deposit
                        </button>
                        <button type="button" @click="setQuickTender(1000)" class="px-2 py-1.5 bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 text-slate-300 text-[11px] font-bold rounded-lg border border-slate-700 transition-colors font-mono">
                            1,000
                        </button>
                        <button type="button" @click="setQuickTender(2000)" class="px-2 py-1.5 bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 text-slate-300 text-[11px] font-bold rounded-lg border border-slate-700 transition-colors font-mono">
                            2,000
                        </button>
                        <button type="button" @click="setQuickTender(5000)" class="px-2 py-1.5 bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 text-slate-300 text-[11px] font-bold rounded-lg border border-slate-700 transition-colors font-mono">
                            5,000
                        </button>
                        <button type="button" @click="advancePayment = ''" class="px-2 py-1.5 bg-slate-800 hover:bg-rose-950/60 hover:text-rose-300 text-slate-400 text-[11px] font-bold rounded-lg border border-slate-700 transition-colors">
                            Clear (0)
                        </button>
                    </div>

                    <!-- Payment Method Selector -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Payment Method</label>
                        <div class="grid grid-cols-2 gap-1.5 text-xs">
                            <label :class="paymentMethod === 'cash' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-750'"
                                   class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                                <span>💵 Cash</span>
                            </label>
                            <label :class="paymentMethod === 'card' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-750'"
                                   class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="sr-only">
                                <span>💳 Card</span>
                            </label>
                            <label :class="paymentMethod === 'bkash' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-750'"
                                   class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="bkash" x-model="paymentMethod" class="sr-only">
                                <span>📱 bKash</span>
                            </label>
                            <label :class="paymentMethod === 'bank_transfer' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-750'"
                                   class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" class="sr-only">
                                <span>🏦 Bank</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Primary Action Button -->
                <div class="pt-5 space-y-2">
                    <button type="submit" 
                            :disabled="!selectedRoomId || isSubmitting"
                            :class="(!selectedRoomId || isSubmitting) ? 'opacity-50 cursor-not-allowed bg-slate-700' : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white shadow-lg shadow-emerald-950/50'"
                            class="w-full py-3.5 px-4 rounded-xl font-black text-sm uppercase tracking-wider flex items-center justify-center gap-2 transition-all transform active:scale-[0.99]">
                        <template x-if="isSubmitting">
                            <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </template>
                        <template x-if="!isSubmitting">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        </template>
                        <span x-text="isSubmitting ? 'Processing Check-In...' : '⚡ Confirm & Check-In'"></span>
                    </button>

                    <div class="flex items-center justify-between text-[10px] text-slate-400 px-1">
                        <span class="flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3 h-3 text-emerald-400"></i>
                            <span>Double-Booking Shield</span>
                        </span>
                        <a href="{{ route('reservations.index') }}" class="hover:text-slate-200 underline">Cancel</a>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
