@extends('layouts.app')

@section('title', 'Auditor & Compliance')
@section('page-header', 'Internal Audit & Compliance')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">Compliance Audit Trail</h2>
            <p class="text-xs text-slate-500">Immutable chronological record of business transactions, privilege changes, and user events</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
            Read-Only Audit Ledger
        </span>
    </div>

    <!-- Audits Table -->
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
                        <th class="py-3 px-4">Change Metadata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($audits as $log)
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-3.5 px-5 font-mono text-[11px] text-slate-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 font-mono">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $log->branch?->name ?? 'Organization-wide' }}</td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">{{ class_basename($log->target_type) }} #{{ $log->target_id }}</td>
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
                        <td colspan="7" class="py-8 text-center text-slate-400">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $audits->links() }}
        </div>
    </div>

</div>
@endsection
