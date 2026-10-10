@extends('layouts.app')

@section('title', 'Rooms & Inventory')
@section('page-header', 'Hotel Rooms & Inventory')

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions Header -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('rooms.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700">
                <option value="">All Operational Statuses</option>
                <option value="available" {{ $status === 'available' ? 'selected' : '' }}>Available</option>
                <option value="cleaning" {{ $status === 'cleaning' ? 'selected' : '' }}>Cleaning / Dirty</option>
                <option value="maintenance" {{ $status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="out_of_order" {{ $status === 'out_of_order' ? 'selected' : '' }}>Out of Order</option>
            </select>

            <select name="type" onchange="this.form.submit()" class="rounded-xl border border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700">
                <option value="">All Room Categories</option>
                @foreach($roomTypes as $rt)
                    <option value="{{ $rt->id }}" {{ $type == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                @endforeach
            </select>

            @if($status || $type)
            <a href="{{ route('rooms.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>

        <a href="{{ route('rooms.calendar') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center justify-center gap-1.5 shrink-0">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            <span>14-Day Availability Matrix</span>
        </a>
    </div>

    <!-- Rooms Grid grouped by Floor -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($rooms as $room)
        @php
            $cardBorder = match($room->operational_status) {
                'available' => 'border-emerald-200 bg-emerald-50/20',
                'cleaning' => 'border-amber-200 bg-amber-50/20',
                'maintenance' => 'border-rose-200 bg-rose-50/20',
                default => 'border-slate-200 bg-slate-50/20',
            };
            $statusBadge = match($room->operational_status) {
                'available' => 'bg-emerald-100 text-emerald-800',
                'cleaning' => 'bg-amber-100 text-amber-800',
                'maintenance' => 'bg-rose-100 text-rose-800',
                default => 'bg-slate-100 text-slate-800',
            };
        @endphp
        <div class="bg-white rounded-2xl border {{ $cardBorder }} p-5 shadow-xs flex flex-col justify-between space-y-4 hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-black text-slate-900 font-heading">Room {{ $room->room_number }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusBadge }}">
                        {{ str_replace('_', ' ', $room->operational_status) }}
                    </span>
                </div>

                <div class="mt-2 text-xs text-slate-600 space-y-1">
                    <p class="font-bold text-slate-800">{{ $room->roomType->name }}</p>
                    <p class="text-slate-400">Floor {{ $room->floor }} &bull; Max {{ $room->roomType->total_capacity }} Guests</p>
                    <p class="font-bold text-emerald-700">BDT {{ number_format($room->roomType->base_price_per_night, 2) }} / night</p>
                </div>

                @if($room->maintenance_reason)
                <p class="mt-2 text-[11px] text-rose-700 bg-rose-50 p-2 rounded-lg border border-rose-100">
                    Reason: {{ $room->maintenance_reason }}
                </p>
                @endif
            </div>

            <!-- Operational Status Toggle Form -->
            <div class="pt-3 border-t border-slate-100">
                <form method="POST" action="{{ route('rooms.status.update', $room) }}" class="space-y-2">
                    @csrf
                    <div class="flex items-center gap-1.5">
                        <select name="operational_status" class="w-full rounded-lg border border-slate-300 py-1.5 px-2 text-[11px] font-semibold text-slate-700">
                            <option value="available" {{ $room->operational_status === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="cleaning" {{ $room->operational_status === 'cleaning' ? 'selected' : '' }}>Cleaning / Dirty</option>
                            <option value="maintenance" {{ $room->operational_status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="out_of_order" {{ $room->operational_status === 'out_of_order' ? 'selected' : '' }}>Out of Order</option>
                        </select>
                        <button type="submit" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-[11px] rounded-lg transition-colors">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <p class="col-span-full py-12 text-center text-xs text-slate-400">No rooms found matching your filter selection.</p>
        @endforelse
    </div>

</div>
@endsection
