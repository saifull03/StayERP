@extends('layouts.app')

@section('title', 'Front Desk Reception')
@section('page-header')
    {{ $activeBranch?->name ?? 'Front Desk' }} — Front Desk Operations
@endsection

@section('content')
<div class="space-y-6">

    <!-- Fast Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-heading">Front Desk Reception Desk</h2>
            <p class="text-xs text-slate-500">Live guest arrivals, departures, room assignments, and walk-in check-in</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reservations.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Walk-In Reservation</span>
            </a>
            <a href="{{ route('rooms.calendar') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>Availability Rack</span>
            </a>
        </div>
    </div>

    <!-- Arrivals & Departures Dual Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Expected Arrivals Today -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-4 bg-emerald-50/60 border-b border-emerald-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-emerald-600 text-white rounded-lg">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 font-heading">Today's Expected Arrivals</h3>
                            <span class="text-[11px] text-emerald-800 font-semibold">{{ $todayArrivals->count() }} Guests expected</span>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($todayArrivals as $res)
                    <div class="p-4 hover:bg-slate-50/80 transition-colors flex items-center justify-between text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm">{{ $res->guest->full_name }}</span>
                                @if($res->guest->vip_status)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-400 text-slate-900">VIP</span>
                                @endif
                            </div>
                            <p class="text-slate-500 font-medium mt-0.5 font-mono text-[11px]">{{ $res->reservation_number }} &bull; {{ $res->guest->phone }}</p>
                            <p class="text-slate-500 text-[11px] mt-0.5">
                                Room: 
                                <span class="font-bold text-slate-800">
                                    {{ $res->reservationRooms->first()?->room?->room_number ?? 'Unassigned' }}
                                </span>
                                ({{ $res->reservationRooms->first()?->roomType?->name }})
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('reservations.checkin', $res) }}">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition-colors flex items-center gap-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Check In</span>
                                </button>
                            </form>
                            <a href="{{ route('reservations.show', $res) }}" class="p-1.5 text-slate-400 hover:text-slate-600 rounded">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-8 text-center">No remaining arrivals scheduled for today.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Expected Departures Today -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-4 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-slate-800 text-white rounded-lg">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 font-heading">Today's Scheduled Departures</h3>
                            <span class="text-[11px] text-slate-500 font-semibold">{{ $todayDepartures->count() }} Guests departing</span>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($todayDepartures as $res)
                    <div class="p-4 hover:bg-slate-50/80 transition-colors flex items-center justify-between text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm">{{ $res->guest->full_name }}</span>
                                <span class="font-bold text-slate-700">Room {{ $res->reservationRooms->first()?->room?->room_number }}</span>
                            </div>
                            <p class="text-slate-500 font-medium mt-0.5 font-mono text-[11px]">{{ $res->reservation_number }}</p>
                            <p class="text-[11px] mt-0.5 {{ $res->balance_due > 0 ? 'text-amber-600 font-bold' : 'text-emerald-600 font-medium' }}">
                                Outstanding Folio Balance: BDT {{ number_format($res->balance_due, 2) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('reservations.checkout', $res) }}">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg shadow-xs transition-colors flex items-center gap-1">
                                    <i data-lucide="door-open" class="w-3.5 h-3.5"></i>
                                    <span>Check Out</span>
                                </button>
                            </form>
                            <a href="{{ route('reservations.show', $res) }}" class="p-1.5 text-slate-400 hover:text-slate-600 rounded">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-8 text-center">No guests scheduled for departure today.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Live Room Rack / Floor Overview -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Live Room Rack Grid</h3>
                <p class="text-xs text-slate-500">Real-time room occupancy and operational status</p>
            </div>
            
            <!-- Legend -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-500"></span> Available</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-blue-600"></span> Occupied</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-amber-500"></span> Cleaning</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-rose-500"></span> Maintenance</span>
            </div>
        </div>

        <!-- Room Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
            @forelse($rooms as $room)
            @php
                $bgClass = match($room->operational_status) {
                    'available' => 'bg-emerald-50 border-emerald-300 text-emerald-950',
                    'cleaning' => 'bg-amber-50 border-amber-300 text-amber-950',
                    'maintenance' => 'bg-rose-50 border-rose-300 text-rose-950',
                    default => 'bg-slate-50 border-slate-300 text-slate-950',
                };
                $badgeClass = match($room->operational_status) {
                    'available' => 'bg-emerald-600 text-white',
                    'cleaning' => 'bg-amber-600 text-white',
                    'maintenance' => 'bg-rose-600 text-white',
                    default => 'bg-slate-600 text-white',
                };
            @endphp
            <div class="p-3 rounded-xl border {{ $bgClass }} flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-base font-black font-heading">{{ $room->room_number }}</span>
                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded {{ $badgeClass }}">
                        {{ substr($room->operational_status, 0, 4) }}
                    </span>
                </div>
                <div class="mt-2 text-[10px] text-slate-600">
                    <p class="truncate font-medium">{{ $room->roomType->name }}</p>
                    <p class="font-mono mt-0.5">Floor {{ $room->floor }}</p>
                </div>
            </div>
            @empty
            <p class="col-span-full py-8 text-center text-xs text-slate-400">No rooms configured in this branch.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
