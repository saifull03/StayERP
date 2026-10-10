@extends('layouts.app')

@section('title', 'Reservations & Stays')
@section('page-header', 'Guest Reservations & Stays')

@section('content')
<div class="space-y-6">

    <!-- Actions & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('reservations.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative min-w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by reservation #, guest name, phone..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>

            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <option value="">All Statuses</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="checked_in" {{ $status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                <option value="checked_out" {{ $status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Apply Filter
            </button>
            @if($search || $status)
            <a href="{{ route('reservations.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>

        @if(auth()->user()->hasPermission('reservations.create'))
        <a href="{{ route('reservations.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-black shadow-md transition-all flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="zap" class="w-4 h-4 text-emerald-200"></i>
            <span>Walk-In POS / New Reservation</span>
        </a>
        @endif
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Reservation #</th>
                        <th class="py-3 px-4">Guest Information</th>
                        <th class="py-3 px-4">Branch</th>
                        <th class="py-3 px-4">Stay Dates</th>
                        <th class="py-3 px-4">Room & Type</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Folio Due</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($reservations as $res)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                            <a href="{{ route('reservations.show', $res) }}" class="hover:text-emerald-700 flex items-center gap-1.5">
                                <span>{{ $res->reservation_number }}</span>
                            </a>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block">{{ $res->guest->full_name }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $res->guest->phone }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700">
                            {{ $res->branch->name }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-700">
                            <span class="block">{{ $res->check_in_date->format('M d, Y') }}</span>
                            <span class="text-[11px] text-slate-400">to {{ $res->check_out_date->format('M d, Y') }} ({{ $res->nights_count }}n)</span>
                        </td>
                        <td class="py-3.5 px-4">
                            @foreach($res->reservationRooms as $rr)
                                <span class="font-bold text-slate-900">Room {{ $rr->room?->room_number ?? 'TBD' }}</span>
                                <span class="text-[11px] text-slate-500 block truncate">{{ $rr->roomType->name }}</span>
                            @endforeach
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'checked_in' => 'bg-emerald-100 text-emerald-800',
                                    'checked_out' => 'bg-slate-100 text-slate-800',
                                    'cancelled' => 'bg-rose-100 text-rose-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses[$res->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ str_replace('_', ' ', $res->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                            @if($res->balance_due > 0)
                                <span class="text-amber-600">BDT {{ number_format($res->balance_due, 2) }}</span>
                            @else
                                <span class="text-emerald-600">Settled</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('reservations.show', $res) }}" class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-emerald-600 hover:text-white rounded-lg transition-colors">
                                    Manage
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">No reservations found matching the criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $reservations->links() }}
        </div>
    </div>

</div>
@endsection
