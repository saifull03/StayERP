@extends('layouts.app')

@section('title', 'Branch Expenses')
@section('page-header', 'Branch Expenses & Expenditure Approvals')

@section('content')
<div class="space-y-6">

    <!-- Header & Create Action -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">Operating Expenditures</h2>
            <p class="text-xs text-slate-500">Track and authorize branch operational supplies, utilities, and repairs</p>
        </div>

        @if(auth()->user()->hasPermission('expenses.create'))
        <div x-data="{ expModal: false, catModal: false, showNewCategory: false }" class="flex items-center gap-2">
            <button @click="catModal = true" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shrink-0 border border-slate-300">
                <i data-lucide="tag" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>+ New Category</span>
            </button>

            <button @click="expModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Record New Expense</span>
            </button>

            <!-- Dedicated Create Category Modal -->
            <div x-show="catModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="catModal = false" class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-slate-900 font-heading">Add Expense Category</h3>
                        <button type="button" @click="catModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Create a standard budget code for branch expenditures.</p>

                    <form method="POST" action="{{ route('expenses.categories.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Category Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Laundry & Linen Dry Cleaning" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Category Code (Optional)</label>
                            <input type="text" name="code" placeholder="e.g. EXP-LNDRY" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold uppercase focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Description</label>
                            <textarea name="description" rows="2" placeholder="Brief notes about this expenditure category..." class="w-full rounded-xl border border-slate-300 p-2 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="catModal = false" class="px-3.5 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg">Create Category</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Expense Record Modal -->
            <div x-show="expModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div @click.away="expModal = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-slate-800">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-lg font-bold text-slate-900 font-heading">Record Operational Expense</h3>
                        <button type="button" @click="expModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Submit branch procurement or utility expense for approval.</p>

                    <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Expense Title *</label>
                            <input type="text" name="title" required placeholder="e.g. Monthly Electricity Utility Bill" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold uppercase text-slate-700">Category *</label>
                                    <button type="button" @click="showNewCategory = !showNewCategory" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700">
                                        <span x-text="showNewCategory ? '← Existing' : '+ New'"></span>
                                    </button>
                                </div>

                                <div x-show="!showNewCategory">
                                    <select name="expense_category_id" x-bind:required="!showNewCategory" class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="showNewCategory" x-cloak>
                                    <input type="text" name="new_category_name" placeholder="Type new category..." class="w-full rounded-xl border border-emerald-400 bg-emerald-50/40 p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Vendor / Payee</label>
                                <select name="vendor_id" class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                                    <option value="">-- No Specific Vendor --</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Amount (BDT) *</label>
                                <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold text-rose-600 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Payment Channel</label>
                                <select name="payment_method" class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-xs font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                                    <option value="cash">💵 Petty Cash</option>
                                    <option value="bank_transfer">🏦 Bank Wire / Cheque</option>
                                    <option value="card">💳 Corporate Card</option>
                                    <option value="mfs">📱 bKash / Nagad</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Expense Incurred Date *</label>
                            <input type="date" name="expense_date" value="{{ now()->toDateString() }}" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Description / Memo</label>
                            <textarea name="description" rows="2" placeholder="Details of purchase or invoice..." class="w-full rounded-xl border border-slate-300 p-2 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="expModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg">Save Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Expense #</th>
                        <th class="py-3 px-4">Title & Category</th>
                        <th class="py-3 px-4">Branch</th>
                        <th class="py-3 px-4">Vendor</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Amount (BDT)</th>
                        <th class="py-3 px-4">Requested By</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($expenses as $exp)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono font-bold text-slate-900">{{ $exp->expense_number }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block">{{ $exp->title }}</span>
                            <span class="text-[11px] text-slate-500 font-medium">{{ $exp->category->name }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $exp->branch->name }}</td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $exp->vendor?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $exp->expense_date->format('M d, Y') }}</td>
                        <td class="py-3.5 px-4 text-right font-black text-rose-600">BDT {{ number_format($exp->amount, 2) }}</td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $exp->requestedBy->name }}</td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $statusBadge = [
                                    'pending_approval' => 'bg-amber-100 text-amber-800',
                                    'approved' => 'bg-emerald-100 text-emerald-800',
                                    'rejected' => 'bg-rose-100 text-rose-800',
                                    'paid' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusBadge[$exp->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ str_replace('_', ' ', $exp->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($exp->status === 'pending_approval' && auth()->user()->hasPermission('expenses.approve'))
                            <div class="flex items-center justify-center gap-1.5">
                                <form method="POST" action="{{ route('expenses.approve', $exp) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="px-2 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('expenses.approve', $exp) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-2 py-1 text-[10px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded">
                                        Reject
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-[11px] text-slate-400">Locked</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">No branch expenses recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $expenses->links() }}
        </div>
    </div>

</div>
@endsection
