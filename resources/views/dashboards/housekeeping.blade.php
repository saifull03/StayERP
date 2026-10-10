@extends('layouts.app')

@section('title', 'Housekeeping Dashboard')
@section('page-header')
    {{ $activeBranch?->name ?? 'Housekeeping' }} — Housekeeping Operations
@endsection

@section('content')
<div class="space-y-6">

    <!-- Status Tally Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-rose-200 bg-rose-50/30 shadow-xs">
            <span class="text-xs font-bold uppercase text-rose-600">Dirty / Checkout</span>
            <p class="text-2xl font-black text-rose-700 font-heading mt-1">{{ $dirtyCount }}</p>
            <span class="text-[11px] text-slate-500 font-medium">Awaiting cleaning</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/30 shadow-xs">
            <span class="text-xs font-bold uppercase text-amber-600">In Cleaning</span>
            <p class="text-2xl font-black text-amber-700 font-heading mt-1">{{ $cleaningCount }}</p>
            <span class="text-[11px] text-slate-500 font-medium">Currently underway</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-blue-200 bg-blue-50/30 shadow-xs">
            <span class="text-xs font-bold uppercase text-blue-600">Awaiting Inspection</span>
            <p class="text-2xl font-black text-blue-700 font-heading mt-1">{{ $inspectCount }}</p>
            <span class="text-[11px] text-slate-500 font-medium">Supervisor review</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-emerald-200 bg-emerald-50/30 shadow-xs">
            <span class="text-xs font-bold uppercase text-emerald-600">Clean & Ready</span>
            <p class="text-2xl font-black text-emerald-700 font-heading mt-1">{{ $cleanCount }}</p>
            <span class="text-[11px] text-slate-500 font-medium">Guest check-in ready</span>
        </div>
    </div>

    <!-- Active Tasks Stream -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Room Cleaning Pipeline</h3>
                <p class="text-xs text-slate-500">Advance operational progress with single-click workflow buttons</p>
            </div>
            <a href="{{ route('housekeeping.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                View Full Housekeeping Register &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($tasks as $task)
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between space-y-3">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-black font-heading text-slate-900">Room {{ $task->room->room_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $task->priority === 'urgent' || $task->priority === 'high' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ $task->priority }} Priority
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 font-medium mt-1">
                        Type: <span class="capitalize">{{ $task->cleaning_type }}</span> &bull; {{ $task->room->roomType->name }}
                    </p>
                    @if($task->notes)
                    <p class="text-[11px] text-slate-500 bg-white p-2 rounded-lg border border-slate-100 mt-2 italic">
                        "{{ $task->notes }}"
                    </p>
                    @endif
                </div>

                <!-- Advance Status Action -->
                <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between">
                    <span class="text-xs font-bold capitalize text-slate-700">Status: {{ str_replace('_', ' ', $task->status) }}</span>

                    @if($task->status === 'dirty')
                    <form method="POST" action="{{ route('housekeeping.status.update', $task) }}">
                        @csrf
                        <input type="hidden" name="status" value="cleaning">
                        <button type="submit" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition-colors">
                            Start Cleaning
                        </button>
                    </form>
                    @elseif($task->status === 'cleaning')
                    <form method="POST" action="{{ route('housekeeping.status.update', $task) }}">
                        @csrf
                        <input type="hidden" name="status" value="awaiting_inspection">
                        <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition-colors">
                            Ready for Inspect
                        </button>
                    </form>
                    @elseif($task->status === 'awaiting_inspection')
                    <form method="POST" action="{{ route('housekeeping.status.update', $task) }}">
                        @csrf
                        <input type="hidden" name="status" value="clean">
                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors">
                            Pass & Mark Clean
                        </button>
                    </form>
                    @else
                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Certified Clean
                    </span>
                    @endif
                </div>
            </div>
            @empty
            <p class="col-span-full py-8 text-center text-xs text-slate-400">No active housekeeping tasks on record.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
