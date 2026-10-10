@extends('layouts.app')

@section('title', 'Notifications')
@section('page-header', 'In-App Operational Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">System & Operational Alerts</h2>
            <p class="text-xs text-slate-500">Real-time alerts for VIP arrivals, departures, shift actions, and maintenance</p>
        </div>

        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                Mark All Read
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs divide-y divide-slate-100 overflow-hidden">
        @forelse($notifications as $notif)
        <div class="p-4 flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-white' : 'bg-emerald-50/30' }}">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-xl {{ $notif->type === 'success' ? 'bg-emerald-100 text-emerald-700' : ($notif->type === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }} mt-0.5 shrink-0">
                    <i data-lucide="{{ $notif->type === 'success' ? 'check-circle' : 'bell' }}" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">{{ $notif->title }}</h3>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $notif->message }}</p>
                    <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </div>

            @if(!$notif->is_read)
            <form method="POST" action="{{ route('notifications.read', $notif) }}">
                @csrf
                <button type="submit" class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 shrink-0">
                    Mark Read
                </button>
            </form>
            @endif
        </div>
        @empty
        <div class="p-12 text-center text-xs text-slate-400">
            No notifications available.
        </div>
        @endforelse
    </div>

    <div>
        {{ $notifications->links() }}
    </div>

</div>
@endsection
