@extends('layouts.app')

@section('title', 'Super Admin Overview')
@section('page-header', 'Super Admin Organization Overview')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Gross Organization Revenue</span>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i data-lucide="circle-dollar-sign" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 font-heading">BDT {{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                    <span>Net Operating: BDT {{ number_format($netOperatingIncome, 2) }}</span>
                </p>
            </div>
        </div>

        <!-- Occupancy Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Current Occupancy</span>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <i data-lucide="bed" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 font-heading">{{ $occupancyRate }}%</p>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    {{ $occupiedRooms }} occupied of {{ $totalRooms }} total rooms
                </p>
            </div>
        </div>

        <!-- Outstanding Balances -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Outstanding Receivables</span>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-amber-600 font-heading">BDT {{ number_format($outstandingBalances, 2) }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1">Unsettled customer invoices</p>
            </div>
        </div>

        <!-- Branches & Rooms -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Operational Branches</span>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <i data-lucide="hotel" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 font-heading">{{ $activeBranches }} / {{ $totalBranches }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1">{{ $totalReservations }} total reservations on record</p>
            </div>
        </div>
    </div>

    <!-- Analytics Charts & Branch Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Branch Revenue Chart (Chart.js) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-heading">Branch Revenue Comparison</h3>
                    <p class="text-xs text-slate-500">Gross revenue distribution across active branches</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Live DB Data</span>
            </div>
            <div class="h-64 relative">
                <canvas id="branchRevenueChart"></canvas>
            </div>
        </div>

        <!-- Quick Organization Summary -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading mb-1">Financial Health</h3>
                <p class="text-xs text-slate-500 mb-4">Core ledger balances summary</p>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs text-slate-600 font-medium">Total Inflow (Payments)</span>
                        <span class="text-sm font-bold text-emerald-700">BDT {{ number_format($totalRevenue, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs text-slate-600 font-medium">Branch Operating Expenses</span>
                        <span class="text-sm font-bold text-rose-600">BDT {{ number_format($totalExpenses, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs text-slate-600 font-medium">Net Operating Margin</span>
                        <span class="text-sm font-bold text-slate-900">BDT {{ number_format($netOperatingIncome, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('reports.index') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-xs transition-colors">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                    <span>Generate Consolidated Reports</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Multi-Branch Performance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Multi-Branch Performance Ranking</h3>
                <p class="text-xs text-slate-500">Comparative metrics across all physical hotel locations</p>
            </div>
            <a href="{{ route('branches.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                Manage Branches &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Branch Name</th>
                        <th class="py-3 px-4">Code / City</th>
                        <th class="py-3 px-4 text-center">Rooms</th>
                        <th class="py-3 px-4 text-center">Occupancy Rate</th>
                        <th class="py-3 px-4 text-right">Revenue (BDT)</th>
                        <th class="py-3 px-4 text-right">Expenses (BDT)</th>
                        <th class="py-3 px-4 text-right">Net Profit (BDT)</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($branchComparisons as $b)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-bold text-slate-900">{{ $b['name'] }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                {{ $b['code'] }} &bull; {{ $b['city'] }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold text-slate-800">{{ $b['total_rooms'] }}</td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $b['occupancy_rate'] >= 50 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ $b['occupancy_rate'] }}%
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-emerald-700">BDT {{ number_format($b['revenue'], 2) }}</td>
                        <td class="py-3.5 px-4 text-right font-semibold text-rose-600">BDT {{ number_format($b['expenses'], 2) }}</td>
                        <td class="py-3.5 px-4 text-right font-extrabold {{ $b['net_income'] >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                            BDT {{ number_format($b['net_income'], 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <form method="POST" action="{{ route('branch.switch') }}">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $b['id'] }}">
                                <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-emerald-600 hover:text-white rounded-lg transition-colors">
                                    Switch & View
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">No branch performance records available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent System Audit Activity -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Recent Audit Logs</h3>
                <p class="text-xs text-slate-500">Security & transaction audit stream across the organization</p>
            </div>
            <a href="{{ route('audit-logs.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                View Full Audit Log &rarr;
            </a>
        </div>
        <div class="space-y-2">
            @forelse($recentAudits as $log)
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                <div class="flex items-center gap-3">
                    <span class="p-1.5 rounded-lg bg-slate-200 text-slate-700 font-mono text-[10px] font-bold">
                        {{ $log->action }}
                    </span>
                    <div>
                        <span class="font-bold text-slate-800">{{ $log->user?->name ?? 'System' }}</span>
                        <span class="text-slate-500 font-normal">performed on {{ $log->branch?->name ?? 'Global' }}</span>
                    </div>
                </div>
                <div class="text-right text-[11px] text-slate-400">
                    {{ $log->created_at->diffForHumans() }} ({{ $log->ip_address }})
                </div>
            </div>
            @empty
            <p class="text-center py-4 text-xs text-slate-400">No recent audit logs.</p>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('branchRevenueChart');
        if (ctx && window.Chart) {
            const branchLabels = {!! json_encode(array_column($branchComparisons, 'name')) !!};
            const branchRevenues = {!! json_encode(array_column($branchComparisons, 'revenue')) !!};
            const branchExpenses = {!! json_encode(array_column($branchComparisons, 'expenses')) !!};

            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: branchLabels,
                    datasets: [
                        {
                            label: 'Revenue (BDT)',
                            data: branchRevenues,
                            backgroundColor: '#059669',
                            borderRadius: 6,
                        },
                        {
                            label: 'Expenses (BDT)',
                            data: branchExpenses,
                            backgroundColor: '#e11d48',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { return 'BDT ' + value.toLocaleString(); }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
