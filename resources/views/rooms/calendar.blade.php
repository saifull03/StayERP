@extends('layouts.app')

@section('title', 'Availability Calendar Matrix')
@section('page-header', '14-Day Room Availability Grid')

@section('content')
<div class="space-y-6">

    <!-- Date Control -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Interactive Availability Calendar</h3>
            <p class="text-xs text-slate-500">Live booking reservations mapped by physical room & stay duration</p>
        </div>

        <form method="GET" action="{{ route('rooms.calendar') }}" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-600">Start Date:</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-xl border border-slate-300 py-1.5 px-3 text-xs font-semibold text-slate-700">
            <button type="submit" class="px-3.5 py-1.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Shift Window
            </button>
            <a href="{{ route('rooms.calendar') }}" class="px-3 py-1.5 text-xs text-slate-500 hover:text-slate-800">Today</a>
        </form>
    </div>

    <!-- Calendar Grid Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="py-3 px-4 font-bold uppercase text-[11px] sticky left-0 z-20 bg-slate-900 min-w-36 border-r border-slate-800">
                            Room #
                        </th>
                        @foreach($dates as $d)
                        <th class="py-2.5 px-2 text-center border-r border-slate-800 min-w-16 {{ $d->isToday() ? 'bg-emerald-800 font-black' : '' }}">
                            <span class="block text-[10px] uppercase font-semibold text-slate-300">{{ $d->format('D') }}</span>
                            <span class="block text-xs font-bold">{{ $d->format('M d') }}</span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- Sticky Room Label -->
                        <td class="py-3 px-4 font-bold text-slate-900 sticky left-0 z-10 bg-white border-r border-slate-200 shadow-xs">
                            <span class="text-sm font-heading block">Room {{ $room->room_number }}</span>
                            <span class="text-[10px] text-slate-400 font-medium truncate block max-w-28">{{ $room->roomType->name }}</span>
                        </td>

                        <!-- Date Cells -->
                        @foreach($dates as $d)
                        @php
                            $dateStr = $d->toDateString();
                            // Check if room is booked on this date
                            $activeBooking = null;
                            foreach($room->reservationRooms as $rr) {
                                $res = $rr->reservation;
                                if ($res && $res->check_in_date->toDateString() <= $dateStr && $res->check_out_date->toDateString() > $dateStr) {
                                    $activeBooking = $res;
                                    break;
                                }
                            }
                        @endphp
                        <td class="p-1 border-r border-slate-100 text-center align-middle {{ $d->isToday() ? 'bg-emerald-50/30' : '' }}">
                            @if($activeBooking)
                                <a href="{{ route('reservations.show', $activeBooking) }}" title="{{ $activeBooking->guest->full_name }} ({{ $activeBooking->reservation_number }})" class="block p-1 rounded-md text-[10px] font-bold truncate bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-2xs">
                                    {{ substr($activeBooking->guest->full_name, 0, 7) }}..
                                </a>
                            @elseif($room->operational_status === 'maintenance')
                                <span class="block p-1 rounded-md text-[9px] font-bold bg-rose-100 text-rose-800">
                                    Maint
                                </span>
                            @elseif($room->operational_status === 'cleaning')
                                <span class="block p-1 rounded-md text-[9px] font-bold bg-amber-100 text-amber-800">
                                    Clean
                                </span>
                            @else
                                <span class="block p-1 rounded-md text-[9px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                    Avail
                                </span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($dates) + 1 }}" class="py-8 text-center text-slate-400 text-xs">No rooms available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
