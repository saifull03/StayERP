@extends('layouts.app')

@section('title', 'Guest Directory')
@section('page-header', 'Guest Profiles & CRM Directory')

@section('content')
<div class="space-y-6">

    <!-- Actions Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('guests.index') }}" class="flex items-center gap-2 flex-1 max-w-md">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search guest by name, phone, email, NID..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
            <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Search
            </button>
            @if($search)
            <a href="{{ route('guests.index') }}" class="px-2 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <!-- New Guest Trigger Modal -->
        <div x-data="{ addGuestModal: false }">
            <button @click="addGuestModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 shrink-0">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Register New Guest</span>
            </button>

            <!-- Modal -->
            <div x-show="addGuestModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="addGuestModal = false" class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 text-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Register New Hotel Guest</h3>
                    <p class="text-xs text-slate-500 mb-4">Record guest identity profile for seamless booking.</p>

                    <form method="POST" action="{{ route('guests.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">First Name *</label>
                                <input type="text" name="first_name" required class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Phone Number *</label>
                                <input type="text" name="phone" required placeholder="+880 1711 000000" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Email Address</label>
                                <input type="email" name="email" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">ID Type</label>
                                <select name="id_type" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                                    <option value="nid">National ID (NID)</option>
                                    <option value="passport">Passport</option>
                                    <option value="driving_license">Driving License</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">ID Number</label>
                                <input type="text" name="id_number" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">City / Address</label>
                            <input type="text" name="address" placeholder="e.g. Dhanmondi, Dhaka" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="vip_status" name="vip_status" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600">
                            <label for="vip_status" class="ml-2 text-xs font-semibold text-slate-700">Flag as VIP Guest</label>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="addGuestModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors">
                                Save Guest Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Guests Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Guest Name</th>
                        <th class="py-3 px-4">Contact Phone</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Identification</th>
                        <th class="py-3 px-4 text-center">VIP Status</th>
                        <th class="py-3 px-4 text-center">Stays Count</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($guests as $g)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-bold text-slate-900">
                            <a href="{{ route('guests.show', $g) }}" class="hover:text-emerald-700">{{ $g->full_name }}</a>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-semibold text-slate-700">{{ $g->phone }}</td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $g->email ?? '—' }}</td>
                        <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">{{ strtoupper($g->id_type) }}: {{ $g->id_number ?? '—' }}</td>
                        <td class="py-3.5 px-4 text-center">
                            @if($g->vip_status)
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-400 text-slate-900">VIP</span>
                            @else
                            <span class="text-slate-400 text-[11px]">Standard</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-900">{{ $g->reservations_count }} Stays</td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('guests.show', $g) }}" class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">
                                View Profile
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No guests found in the directory.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $guests->links() }}
        </div>
    </div>

</div>
@endsection
