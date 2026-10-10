@extends('layouts.app')

@section('title', $guest->full_name . ' | Guest Profile')
@section('page-header', 'Guest Profile: ' . $guest->full_name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-2">
        <a href="{{ route('guests.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Guest Directory</span>
        </a>
    </div>

    <!-- Profile Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold text-xl uppercase font-heading shadow-md">
                {{ substr($guest->first_name, 0, 1) }}{{ substr($guest->last_name ?? '', 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black text-slate-900 font-heading">{{ $guest->full_name }}</h2>
                    @if($guest->vip_status)
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-400 text-slate-900">VIP GUEST</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $guest->phone }} &bull; {{ $guest->email ?? 'No email provided' }} &bull; {{ strtoupper($guest->id_type) }}: {{ $guest->id_number ?? 'N/A' }}
                </p>
                @if($guest->address)
                <p class="text-xs text-slate-400 mt-0.5">{{ $guest->address }}, {{ $guest->city ?? '' }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="text-[11px] text-slate-400 font-medium block">Total Lifetime Stays</span>
                <span class="text-xl font-black text-emerald-700 font-heading">{{ $guest->reservations->count() }} Stays</span>
            </div>
        </div>
    </div>

    <!-- Stay History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-heading">Stay History Across All Branches</h3>
            <p class="text-xs text-slate-500">Historical stays and reservations across the organization</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Reservation #</th>
                        <th class="py-3 px-4">Branch</th>
                        <th class="py-3 px-4">Dates</th>
                        <th class="py-3 px-4">Room Category</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Total Charges</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($guest->reservations as $res)
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-3.5 px-5 font-mono font-bold text-slate-900">{{ $res->reservation_number }}</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $res->branch->name }}</td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $res->check_in_date->format('M d') }} - {{ $res->check_out_date->format('M d, Y') }}</td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            {{ $res->reservationRooms->first()?->roomType?->name ?? 'Standard' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-800">
                                {{ str_replace('_', ' ', $res->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-slate-900">BDT {{ number_format($res->total_charges, 2) }}</td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('reservations.show', $res) }}" class="px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg">
                                View Stay
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No past stays recorded for this guest.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
