@extends('layouts.app')

@section('title', 'Branch Manager Operations')
@section('page-header')
    {{ $activeBranch?->name ?? 'Branch' }} — Operations Hub
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Rooms -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-slate-500">Total Rooms</span>
            <p class="text-2xl font-black text-slate-900 mt-1 font-heading">{{ $totalRooms }}</p>
            <span class="text-[11px] text-slate-400 font-medium">Physical inventory</span>
        </div>

        <!-- Available Rooms -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-emerald-600">Available</span>
            <p class="text-2xl font-black text-emerald-600 mt-1 font-heading">{{ $availableRooms }}</p>
            <span class="text-[11px] text-slate-400 font-medium">Ready for guests</span>
        </div>

        <!-- Occupied Rooms -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-blue-600">Occupied</span>
            <p class="text-2xl font-black text-blue-600 mt-1 font-heading">{{ $occupiedRooms }}</p>
            <span class="text-[11px] text-slate-400 font-medium">Checked in</span>
        </div>

        <!-- Cleaning Rooms -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-amber-600">Housekeeping</span>
            <p class="text-2xl font-black text-amber-600 mt-1 font-heading">{{ $cleaningRooms }}</p>
            <span class="text-[11px] text-slate-400 font-medium">Dirty / in-cleaning</span>
        </div>

        <!-- Maintenance Rooms -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-rose-600">Maintenance</span>
            <p class="text-2xl font-black text-rose-600 mt-1 font-heading">{{ $maintenanceRooms }}</p>
            <span class="text-[11px] text-slate-400 font-medium">Blocked rooms</span>
        </div>

        <!-- Branch Revenue -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-purple-600">Revenue</span>
            <p class="text-xl font-black text-purple-700 mt-1 font-heading truncate">BDT {{ number_format($branchRevenue, 0) }}</p>
            <span class="text-[11px] text-slate-400 font-medium">Total collected</span>
        </div>
    </div>

    <!-- Daily Flow: Arrivals & Departures -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-r from-emerald-900 to-teal-900 text-white p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-bold text-emerald-300 tracking-wider">Today's Expected Arrivals</p>
                <h3 class="text-3xl font-extrabold font-heading mt-1">{{ $todayArrivals }} Guests</h3>
                <p class="text-xs text-slate-300 mt-1">Pending front desk check-in processing</p>
            </div>
            <a href="{{ route('reservations.index', ['status' => 'confirmed']) }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors">
                View Arrivals &rarr;
            </a>
        </div>

        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-bold text-slate-300 tracking-wider">Today's Scheduled Departures</p>
                <h3 class="text-3xl font-extrabold font-heading mt-1">{{ $todayDepartures }} Guests</h3>
                <p class="text-xs text-slate-300 mt-1">Pending check-out & folio settlements</p>
            </div>
            <a href="{{ route('reservations.index', ['status' => 'checked_in']) }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors">
                View Departures &rarr;
            </a>
        </div>
    </div>

    <!-- Operational Split: Housekeeping Workload & Open Maintenance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Housekeeping Workload -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-900 font-heading flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                        <span>Pending Housekeeping Tasks</span>
                    </h3>
                    <a href="{{ route('housekeeping.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Board &rarr;
                    </a>
                </div>
                <div class="space-y-2">
                    @forelse($pendingHousekeeping as $task)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div>
                            <span class="font-bold text-slate-900">Room {{ $task->room->room_number }}</span>
                            <span class="text-slate-500 font-medium ml-2">({{ ucfirst($task->cleaning_type) }})</span>
                            <p class="text-[11px] text-slate-400 mt-0.5">Assigned: {{ $task->assignedTo?->name ?? 'Unassigned' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $task->status === 'dirty' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $task->status }}
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No pending cleaning tasks at this branch.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Open Maintenance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-900 font-heading flex items-center gap-2">
                        <i data-lucide="wrench" class="w-4 h-4 text-rose-500"></i>
                        <span>Active Maintenance Issues</span>
                    </h3>
                    <a href="{{ route('maintenance.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Board &rarr;
                    </a>
                </div>
                <div class="space-y-2">
                    @forelse($openMaintenance as $mReq)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900">{{ $mReq->title }}</span>
                                @if($mReq->blocks_room)
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-600 text-white">Blocks Room</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">Room: {{ $mReq->room?->room_number ?? 'General' }} &bull; Assigned: {{ $mReq->assignedTo?->name ?? 'Unassigned' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800">
                            {{ $mReq->priority }}
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No active maintenance tickets reported.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Branch Reservations -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading">Recent Branch Reservations</h3>
                <p class="text-xs text-slate-500">Live booking arrivals and stays</p>
            </div>
            <a href="{{ route('reservations.create') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors">
                + New Reservation
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Reservation #</th>
                        <th class="py-3 px-4">Guest</th>
                        <th class="py-3 px-4">Dates</th>
                        <th class="py-3 px-4">Room</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Balance Due</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($recentReservations as $res)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-bold text-slate-900 font-mono">{{ $res->reservation_number }}</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $res->guest->full_name }}</td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $res->check_in_date->format('M d') }} - {{ $res->check_out_date->format('M d, Y') }}</td>
                        <td class="py-3.5 px-4">
                            @foreach($res->reservationRooms as $rr)
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-800">
                                    {{ $rr->room?->room_number ?? 'Auto' }}
                                </span>
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
                            BDT {{ number_format($res->balance_due, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('reservations.show', $res) }}" class="px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">No reservations recorded yet for this branch.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
