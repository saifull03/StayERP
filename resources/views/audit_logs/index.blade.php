@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-header', 'Security & Operational Audit Trails')

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="flex items-center gap-2 flex-1 max-w-lg">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by action, user name, IP address..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
            <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Search
            </button>
            @if($search)
            <a href="{{ route('audit-logs.index') }}" class="px-2 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <span class="text-xs text-slate-400 font-mono">
            Immutable Audit Trail
        </span>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-5">Timestamp</th>
                        <th class="py-3 px-4">Actor</th>
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-4">Branch Context</th>
                        <th class="py-3 px-4">Target Entity</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4">Metadata Payload</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono text-[11px] text-slate-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 font-mono">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700">{{ $log->branch?->name ?? 'Organization Global' }}</td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                            {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-500">{{ $log->ip_address }}</td>
                        <td class="py-3.5 px-4">
                            @if($log->new_values)
                            <pre class="text-[10px] bg-slate-100 p-1.5 rounded text-slate-700 max-w-xs overflow-x-auto font-mono">{{ json_encode($log->new_values) }}</pre>
                            @else
                            <span class="text-slate-400 italic">No diff</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No audit records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
