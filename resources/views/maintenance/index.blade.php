@extends('layouts.app')

@section('title', 'Maintenance Board')
@section('page-header', 'Facility Maintenance & Equipment')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">Maintenance & Facility Repairs</h2>
            <p class="text-xs text-slate-500">Log repair tickets, block rooms from booking, and record actual work costs</p>
        </div>

        <div x-data="{ maintModal: false }">
            <button @click="maintModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Log Maintenance Issue</span>
            </button>

            <!-- Modal -->
            <div x-show="maintModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="maintModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Log Maintenance Issue</h3>
                    <p class="text-xs text-slate-500 mb-4">Create repair ticket and optionally block room from guest booking.</p>

                    <form method="POST" action="{{ route('maintenance.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Issue Title *</label>
                            <input type="text" name="title" required placeholder="e.g. AC Thermostat malfunction" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-medium">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Room (if applicable)</label>
                                <select name="room_id" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                    <option value="">-- Common Area / Plant --</option>
                                    @foreach($rooms as $r)
                                        <option value="{{ $r->id }}">Room {{ $r->room_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Priority</label>
                                <select name="priority" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Assign Engineer</label>
                                <select name="assigned_to_user_id" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-medium">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($staff as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Estimated Cost (BDT)</label>
                                <input type="number" step="0.01" name="estimated_cost" placeholder="0.00" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div class="flex items-center p-3 rounded-xl bg-rose-50 border border-rose-200">
                            <input type="checkbox" id="blocks_room" name="blocks_room" value="1" class="h-4 w-4 rounded border-rose-300 text-rose-600">
                            <label for="blocks_room" class="ml-2 text-xs font-bold text-rose-900">
                                Block this physical room from reservations until resolved
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Detailed Defect Description *</label>
                            <textarea name="description" required rows="3" placeholder="Describe mechanical, electrical, or plumbing defect..." class="w-full rounded-xl border border-slate-300 p-2.5 text-xs"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="maintModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg">Log Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Ticket #</th>
                        <th class="py-3 px-4">Title & Description</th>
                        <th class="py-3 px-4">Location / Room</th>
                        <th class="py-3 px-4">Priority</th>
                        <th class="py-3 px-4">Assigned Engineer</th>
                        <th class="py-3 px-4 text-center">Blocks Room</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono font-bold text-slate-900">#{{ $req->id }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block">{{ $req->title }}</span>
                            <span class="text-[11px] text-slate-500 block truncate max-w-xs">{{ $req->description }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">
                            {{ $req->room ? 'Room ' . $req->room->room_number : 'Common Facility' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $req->priority === 'critical' || $req->priority === 'high' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ $req->priority }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-800">{{ $req->assignedTo?->name ?? 'Unassigned' }}</td>
                        <td class="py-3.5 px-4 text-center">
                            @if($req->blocks_room)
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-600 text-white">Blocked</span>
                            @else
                            <span class="text-slate-400">No</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $statusBadge = [
                                    'open' => 'bg-rose-100 text-rose-800',
                                    'assigned' => 'bg-amber-100 text-amber-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'resolved' => 'bg-emerald-100 text-emerald-800',
                                    'closed' => 'bg-slate-100 text-slate-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusBadge[$req->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($req->status !== 'resolved' && $req->status !== 'closed')
                            <form method="POST" action="{{ route('maintenance.status.update', $req) }}">
                                @csrf
                                <input type="hidden" name="status" value="resolved">
                                <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg">
                                    Resolve
                                </button>
                            </form>
                            @else
                            <span class="text-[11px] text-emerald-600 font-semibold">Done</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">No maintenance tickets found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $requests->links() }}
        </div>
    </div>

</div>
@endsection
