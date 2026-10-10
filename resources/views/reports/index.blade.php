@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-header', 'Organization & Branch Reports')

@section('content')
<div class="space-y-6">

    <!-- Filters & Export Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-xl border border-slate-300 py-1.5 px-3 text-xs font-semibold text-slate-700">
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-xl border border-slate-300 py-1.5 px-3 text-xs font-semibold text-slate-700">
            </div>

            @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">Branch Filter</label>
                <select name="branch_id" class="rounded-xl border border-slate-300 py-1.5 px-3 text-xs font-semibold text-slate-700">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="self-end">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                    Generate Report
                </button>
            </div>
        </form>

        <!-- CSV Export Buttons -->
        <div class="flex items-center gap-2 self-end md:self-auto">
            <a href="{{ route('reports.export', ['type' => 'revenue', 'branch_id' => $branchFilter]) }}" class="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 flex items-center gap-1.5 transition-colors">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Export Revenue CSV</span>
            </a>
            <a href="{{ route('reports.export', ['type' => 'reservations', 'branch_id' => $branchFilter]) }}" class="px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-800 font-bold text-xs rounded-xl border border-slate-200 flex items-center gap-1.5 transition-colors">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Export Bookings CSV</span>
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Filtered Total Revenue</span>
            <p class="text-2xl font-black text-emerald-700 mt-1 font-heading">BDT {{ number_format($totalRevenue, 2) }}</p>
            <span class="text-xs text-slate-400 font-medium">{{ $startDate }} to {{ $endDate }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Filtered Total Expenses</span>
            <p class="text-2xl font-black text-rose-600 mt-1 font-heading">BDT {{ number_format($totalExpenses, 2) }}</p>
            <span class="text-xs text-slate-400 font-medium">Approved branch expenses</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Net Operating Inflow</span>
            <p class="text-2xl font-black text-slate-900 mt-1 font-heading">BDT {{ number_format($totalRevenue - $totalExpenses, 2) }}</p>
            <span class="text-xs text-slate-400 font-medium">Revenue minus expenses</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Reservations Created</span>
            <p class="text-2xl font-black text-purple-700 mt-1 font-heading">{{ $totalReservations }} Bookings</p>
            <span class="text-xs text-slate-400 font-medium">{{ $checkedInCount }} Checked In, {{ $cancelledCount }} Cancelled</span>
        </div>
    </div>

    <!-- Multi-Branch Comparative Breakdown -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-heading">Branch Comparative Performance Breakdown</h3>
            <p class="text-xs text-slate-500">Comparative revenue and expenses for selected date window</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Branch Name</th>
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4 text-center">Room Inventory</th>
                        <th class="py-3 px-4 text-right">Collections (BDT)</th>
                        <th class="py-3 px-4 text-right">Expenses (BDT)</th>
                        <th class="py-3 px-4 text-right">Net Income (BDT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($branchPerformance as $bp)
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-3.5 px-5 font-bold text-slate-900">{{ $bp['branch']->name }}</td>
                        <td class="py-3.5 px-4 font-mono font-bold">{{ $bp['branch']->code }}</td>
                        <td class="py-3.5 px-4 text-center font-semibold text-slate-800">{{ $bp['rooms'] }} Rooms</td>
                        <td class="py-3.5 px-4 text-right font-black text-emerald-700">BDT {{ number_format($bp['revenue'], 2) }}</td>
                        <td class="py-3.5 px-4 text-right font-bold text-rose-600">BDT {{ number_format($bp['expenses'], 2) }}</td>
                        <td class="py-3.5 px-4 text-right font-extrabold text-slate-900">BDT {{ number_format($bp['net_income'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
