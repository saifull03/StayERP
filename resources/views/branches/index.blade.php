@extends('layouts.app')

@section('title', 'Branch Management')
@section('page-header', 'Hotel Branch Management')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">Hotel Operating Branches</h2>
            <p class="text-xs text-slate-500">Configure multi-branch locations, tax policies, and operating hours</p>
        </div>

        <div x-data="{ branchModal: false }">
            <button @click="branchModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Register New Branch</span>
            </button>

            <!-- Modal -->
            <div x-show="branchModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="branchModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Create Hotel Branch</h3>
                    <p class="text-xs text-slate-500 mb-4">Add a new operational location to StayingHotel ERP.</p>

                    <form method="POST" action="{{ route('branches.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Branch Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Khulna City Branch" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Unique Code *</label>
                                <input type="text" name="code" required maxlength="10" placeholder="e.g. KHU" class="w-full rounded-xl border border-slate-300 p-2 text-xs uppercase font-mono font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">City *</label>
                                <input type="text" name="city" required placeholder="e.g. Khulna" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Contact Phone</label>
                                <input type="text" name="phone" placeholder="+880 1711 000000" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Official Email</label>
                                <input type="email" name="email" placeholder="branch@stayinghotel.com" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Physical Address</label>
                            <input type="text" name="address" placeholder="e.g. 12 KDA Avenue" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">VAT/Tax (%) *</label>
                                <input type="number" step="0.01" name="tax_rate_percent" value="15.00" required class="w-full rounded-xl border border-slate-300 p-2 text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Check-In</label>
                                <input type="text" name="checkin_time" value="14:00" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Check-Out</label>
                                <input type="text" name="checkout_time" value="12:00" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="branchModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg">Register Branch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($branches as $b)
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4 hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-black bg-slate-900 text-white">
                        {{ $b->code }}
                    </span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $b->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $b->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <h3 class="text-base font-extrabold text-slate-900 font-heading mt-3">{{ $b->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $b->address }}, {{ $b->city }}</p>

                <div class="mt-4 pt-3 border-t border-slate-100 space-y-1.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Physical Rooms:</span>
                        <span class="font-bold text-slate-900">{{ $b->rooms_count }} Rooms</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Stays:</span>
                        <span class="font-bold text-slate-900">{{ $b->reservations_count }} Bookings</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Staff Assigned:</span>
                        <span class="font-bold text-slate-900">{{ $b->user_assignments_count }} Employees</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Hotel Tax Rate:</span>
                        <span class="font-bold text-emerald-700">{{ $b->tax_rate_percent }}%</span>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <form method="POST" action="{{ route('branch.switch') }}">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $b->id }}">
                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-emerald-600 hover:text-white rounded-lg transition-colors">
                        Select Branch
                    </button>
                </form>

                <form method="POST" action="{{ route('branches.toggle', $b) }}">
                    @csrf
                    <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold {{ $b->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg">
                        {{ $b->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="col-span-full py-8 text-center text-xs text-slate-400">No branches registered.</p>
        @endforelse
    </div>

</div>
@endsection
