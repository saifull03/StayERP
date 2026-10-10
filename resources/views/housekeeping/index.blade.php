@extends('layouts.app')

@section('title', 'Housekeeping Operations')
@section('page-header', 'Housekeeping & Turnover Board')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">Room Turnover Operations</h2>
            <p class="text-xs text-slate-500">Track and advance room cleaning tasks from Dirty to Certified Clean</p>
        </div>

        <div x-data="{ taskModal: false }">
            <button @click="taskModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Assign Cleaning Task</span>
            </button>

            <!-- Modal -->
            <div x-show="taskModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="taskModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Schedule Housekeeping Task</h3>
                    <p class="text-xs text-slate-500 mb-4">Assign room cleaning to staff member.</p>

                    <form method="POST" action="{{ route('housekeeping.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Select Room *</label>
                            <select name="room_id" required class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}">Room {{ $r->room_number }} (Floor {{ $r->floor }} - {{ $r->operational_status }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Assign Staff</label>
                                <select name="assigned_to_user_id" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-medium">
                                    <option value="">-- Unassigned (General Pool) --</option>
                                    @foreach($staff as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Priority</label>
                                <select name="priority" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Cleaning Type</label>
                            <select name="cleaning_type" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                <option value="checkout">Checkout Full Turnover</option>
                                <option value="stayover">Stayover Daily Cleaning</option>
                                <option value="deep_clean">Periodic Deep Sanitation</option>
                                <option value="touchup">Inspection Touchup</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Special Notes</label>
                            <textarea name="notes" rows="2" placeholder="e.g. VIP guest arrival at 3pm, replace bathrobes" class="w-full rounded-xl border border-slate-300 p-2 text-xs"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="taskModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg">Assign Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Housekeeping Tasks Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Room #</th>
                        <th class="py-3 px-4">Room Type</th>
                        <th class="py-3 px-4">Cleaning Type</th>
                        <th class="py-3 px-4">Priority</th>
                        <th class="py-3 px-4">Assigned Attendant</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Notes</th>
                        <th class="py-3 px-4 text-center">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-black text-slate-900 text-sm">Room {{ $task->room->room_number }}</td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $task->room->roomType->name }}</td>
                        <td class="py-3.5 px-4 capitalize">{{ $task->cleaning_type }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $task->priority === 'urgent' || $task->priority === 'high' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $statusBadge = [
                                    'dirty' => 'bg-rose-100 text-rose-800',
                                    'cleaning' => 'bg-amber-100 text-amber-800',
                                    'awaiting_inspection' => 'bg-blue-100 text-blue-800',
                                    'clean' => 'bg-emerald-100 text-emerald-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusBadge[$task->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 italic max-w-xs truncate">{{ $task->notes ?? '—' }}</td>
                        <td class="py-3.5 px-4 text-center">
                            <form method="POST" action="{{ route('housekeeping.status.update', $task) }}" class="inline-block">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 py-1 px-2 text-[11px] font-bold text-slate-700">
                                    <option value="dirty" {{ $task->status === 'dirty' ? 'selected' : '' }}>Dirty</option>
                                    <option value="cleaning" {{ $task->status === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                    <option value="awaiting_inspection" {{ $task->status === 'awaiting_inspection' ? 'selected' : '' }}>Inspection</option>
                                    <option value="clean" {{ $task->status === 'clean' ? 'selected' : '' }}>Clean</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">No housekeeping records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $tasks->links() }}
        </div>
    </div>

</div>
@endsection
