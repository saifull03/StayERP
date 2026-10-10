<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $payment->payment_number }} | StayingHotel ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .receipt-mono { font-family: 'Space Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .receipt-sheet { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 py-10 px-4">

    <!-- Actions for Screen -->
    <div class="max-w-md mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ url()->previous() }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back</span>
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Print Receipt</span>
        </button>
    </div>

    <!-- Thermal Receipt Card -->
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 p-8 receipt-sheet receipt-mono text-slate-900 text-xs">
        
        <!-- Center Header -->
        <div class="text-center pb-4 border-b border-dashed border-slate-300">
            <h1 class="text-base font-black uppercase tracking-wider">StayingHotel ERP</h1>
            <p class="font-bold mt-0.5">{{ $payment->branch->name }}</p>
            <p class="text-[11px] text-slate-500">{{ $payment->branch->address }}</p>
            <p class="text-[11px] text-slate-500">Tel: {{ $payment->branch->phone }}</p>
            <div class="mt-2 inline-block px-3 py-1 bg-slate-100 rounded text-[11px] font-bold uppercase tracking-wider">
                Official Payment Receipt
            </div>
        </div>

        <!-- Receipt Metadata -->
        <div class="py-4 border-b border-dashed border-slate-300 space-y-1 text-[11px]">
            <div class="flex justify-between">
                <span>Receipt Number:</span>
                <span class="font-bold">{{ $payment->payment_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Payment Date:</span>
                <span>{{ $payment->paid_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Guest Name:</span>
                <span class="font-bold">{{ $payment->reservation->guest->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Reservation Ref:</span>
                <span>{{ $payment->reservation->reservation_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Room Assigned:</span>
                <span>Room {{ $payment->reservation->reservationRooms->first()?->room?->room_number ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Cashier / Staff:</span>
                <span>{{ $payment->receivedBy?->name ?? 'Front Desk Cashier' }}</span>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="py-4 border-b border-dashed border-slate-300 space-y-2">
            <div class="flex justify-between">
                <span class="font-bold uppercase">Payment Method:</span>
                <span class="font-bold uppercase">{{ strtoupper($payment->payment_method) }}</span>
            </div>
            @if($payment->transaction_reference)
            <div class="flex justify-between text-[11px] text-slate-600">
                <span>Voucher / Ref:</span>
                <span>{{ $payment->transaction_reference }}</span>
            </div>
            @endif
            @if($payment->cashier_shift_id)
            <div class="flex justify-between text-[11px] text-slate-600">
                <span>Cashier Shift:</span>
                <span>{{ $payment->cashierShift?->shift_number }}</span>
            </div>
            @endif

            <div class="flex justify-between items-baseline pt-2 text-base font-black border-t border-slate-200">
                <span>AMOUNT PAID:</span>
                <span>BDT {{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <!-- Reservation Balance Status -->
        <div class="py-4 border-b border-dashed border-slate-300 space-y-1 text-[11px]">
            <div class="flex justify-between">
                <span>Total Stay Charges:</span>
                <span>BDT {{ number_format($payment->reservation->total_charges, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total Received to Date:</span>
                <span>BDT {{ number_format($payment->reservation->total_paid, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-xs pt-1 border-t border-slate-100">
                <span>Remaining Folio Balance:</span>
                <span>BDT {{ number_format($payment->reservation->balance_due, 2) }}</span>
            </div>
        </div>

        <!-- Footer Sign-off -->
        <div class="pt-6 text-center text-[10px] text-slate-500 space-y-1">
            <p class="font-bold text-slate-800">Thank you for staying at StayingHotel!</p>
            <p>Please retain this receipt for checkout reconciliation.</p>
            <p class="text-[9px] text-slate-400">Generated by StayingHotel ERP &bull; Verified</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.createIcons && window.lucideIcons) {
                window.createIcons({ icons: window.lucideIcons });
            }
        });
    </script>
</body>
</html>
