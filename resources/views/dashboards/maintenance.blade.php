@extends('layouts.app')

@section('title', 'Maintenance Hub')
@section('page-header')
    {{ $activeBranch?->name ?? 'Maintenance' }} — Maintenance & Facilities
@endsection

@section('content')
<div class="space-y-6">

    <!-- Status Tally Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-xl border border-rose-200 shadow-xs">
            <span class="text-xs font-bold uppercase text-rose-600">Open Tickets</span>
            <p class="text-2xl font-black text-rose-700 font-heading mt-1">{{ $openCount }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-xs">
            <span class="text-xs font-bold uppercase text-amber-600">Assigned</span>
            <p class="text-2xl font-black text-amber-700 font-heading mt-1">{{ $assignedCount }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-blue-200 shadow-xs">
            <span class="text-xs font-bold uppercase text-blue-600">In Progress</span>
            <p class="text-2xl font-black text-blue-700 font-heading mt-1">{{ $inProgressCount }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-emerald-200 shadow-xs">
            <span class="text-xs font-bold uppercase text-emerald-600">Resolved</span>
            <p class="text-2xl font-black text-emerald-700 font-heading mt-1">{{ $resolvedCount }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-purple-200 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-xs font-bold uppercase text-purple-600">Blocked Rooms</span>
            <p class="text-2xl font-black text-purple-700 font-heading mt-1">{{ $blockedRoomsCount }}</p>
        </div>
    </div>

    <!-- Maintenance Issues Stream -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Maintenance Task Requests</h3>
                <p class="text-xs text-slate-500">Facility repairs, HVAC diagnostics, and room blocking management</p>
            </div>
            <a href="{{ route('maintenance.index') }}" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors">
                + Create New Ticket
            </a>
        </div>

        <div class="space-y-3">
            @forelse($requests as $req)
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm">{{ $req->title }}</span>
                        @if($req->blocks_room)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">Blocks Guest Booking</span>
                        @endif
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $req->priority === 'critical' || $req->priority === 'high' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ $req->priority }} Priority
                        </span>
                    </div>
                    <p class="text-xs text-slate-600">{{ $req->description }}</p>
                    <p class="text-[11px] text-slate-400">
                        Room: {{ $req->room?->room_number ?? 'Common Area' }} &bull; 
                        Assigned To: {{ $req->assignedTo?->name ?? 'Unassigned' }} &bull; 
                        Reported by: {{ $req->reportedBy->name }}
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($req->status !== 'resolved' && $req->status !== 'closed')
                    <form method="POST" action="{{ route('maintenance.status.update', $req) }}">
                        @csrf
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors flex items-center gap-1">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>Mark Resolved</span>
                        </button>
                    </form>
                    @else
                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                        <i data-lucide="check-check" class="w-4 h-4"></i> Resolved
                    </span>
                    @endif
                </div>
            </div>
            @empty
            <p class="py-8 text-center text-xs text-slate-400">No maintenance tickets logged.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
