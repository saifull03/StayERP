@extends('layouts.app')

@section('title', 'Staff & Roles')
@section('page-header', 'Employee Directory & Access Control')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">Staff & Role Allocations</h2>
            <p class="text-xs text-slate-500">Manage employee accounts, branch permissions, and role designations</p>
        </div>

        <div x-data="{ staffModal: false }">
            <button @click="staffModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 shrink-0">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Register Staff Member</span>
            </button>

            <!-- Modal -->
            <div x-show="staffModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="staffModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 font-heading mb-1">Add Staff Account</h3>
                    <p class="text-xs text-slate-500 mb-4">Create login credentials and allocate branch responsibility.</p>

                    <form method="POST" action="{{ route('staff.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Full Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Shakil Ahmed" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-medium">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Work Email *</label>
                                <input type="email" name="email" required placeholder="shakil@stayinghotel.com" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Phone Number *</label>
                                <input type="text" name="phone" required placeholder="+880 1711 000000" class="w-full rounded-xl border border-slate-300 p-2 text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Official Designation *</label>
                            <input type="text" name="designation" required placeholder="e.g. Assistant Front Desk Manager" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-medium">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Role Allocation *</label>
                                <select name="role_id" required class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                    @foreach($roles as $r)
                                        @if($r->slug !== 'super-admin' || auth()->user()->isSuperAdmin())
                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Assigned Branch</label>
                                <select name="branch_id" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-semibold">
                                    <option value="">🏢 Global (All Branches)</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Initial Password *</label>
                            <input type="password" name="password" required minlength="8" value="password" class="w-full rounded-xl border border-slate-300 p-2 text-xs font-mono">
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="staffModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg">Register Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Employee Name</th>
                        <th class="py-3 px-4">Designation</th>
                        <th class="py-3 px-4">Email & Phone</th>
                        <th class="py-3 px-4">Role Privileges</th>
                        <th class="py-3 px-4">Branch Assignment</th>
                        <th class="py-3 px-4 text-center">Active Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($staffMembers as $member)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-bold text-slate-900">{{ $member->name }}</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $member->designation ?? 'Staff' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="block text-slate-800">{{ $member->email }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $member->phone ?? '—' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            @foreach($member->roles as $role)
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-800">
                                {{ $role->name }}
                            </span>
                            @endforeach
                        </td>
                        <td class="py-3.5 px-4 text-slate-700">
                            @if($member->isSuperAdmin())
                            <span class="font-bold text-emerald-700">All Branches (Global)</span>
                            @else
                            {{ $member->getPrimaryBranch()?->name ?? 'Unassigned' }}
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $member->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $member->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($member->id !== auth()->id())
                            <form method="POST" action="{{ route('staff.toggle', $member) }}">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 text-[11px] font-bold {{ $member->is_active ? 'text-rose-600 bg-rose-50 hover:bg-rose-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }} rounded-lg transition-colors">
                                    {{ $member->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            @else
                            <span class="text-[11px] text-slate-400">Current User</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No staff members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $staffMembers->links() }}
        </div>
    </div>

</div>
@endsection
