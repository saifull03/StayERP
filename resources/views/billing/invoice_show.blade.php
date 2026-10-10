<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} | StayingHotel ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 py-10 px-4 sm:px-6">

    <!-- Top Floating Actions for Screen -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('billing.invoices') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Invoices</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Tax Invoice</span>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-200/80 p-8 sm:p-12 print-card text-slate-800">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between border-b border-slate-200 pb-8 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold">
                        <i data-lucide="hotel" class="w-4 h-4"></i>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 font-heading">StayingHotel ERP</span>
                </div>
                <p class="text-sm font-bold text-slate-800">{{ $invoice->branch->name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $invoice->branch->address }}, {{ $invoice->branch->city }}</p>
                <p class="text-xs text-slate-500">Phone: {{ $invoice->branch->phone }} &bull; Email: {{ $invoice->branch->email }}</p>
                <p class="text-xs text-slate-400 mt-1">VAT Reg / BIN: 002938472-0101</p>
            </div>

            <div class="sm:text-right">
                <span class="text-2xl font-black text-slate-900 font-heading uppercase tracking-wider block">Tax Invoice</span>
                <p class="text-sm font-mono font-bold text-emerald-700 mt-1">{{ $invoice->invoice_number }}</p>
                <div class="mt-3 text-xs text-slate-500 space-y-0.5">
                    <p><span class="font-bold text-slate-700">Issue Date:</span> {{ $invoice->issued_date->format('F d, Y') }}</p>
                    @if($invoice->due_date)
                    <p><span class="font-bold text-slate-700">Payment Due:</span> {{ $invoice->due_date->format('F d, Y') }}</p>
                    @endif
                    <p><span class="font-bold text-slate-700">Reservation Ref:</span> {{ $invoice->reservation->reservation_number }}</p>
                </div>
            </div>
        </div>

        <!-- Bill To & Stay Parameters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-200 text-xs">
            <div>
                <span class="font-bold uppercase tracking-wider text-slate-400 text-[10px] block mb-1">Billed To (Guest):</span>
                <p class="text-base font-black text-slate-900 font-heading">{{ $invoice->reservation->guest->full_name }}</p>
                <p class="text-slate-600 mt-1">Phone: {{ $invoice->reservation->guest->phone }}</p>
                @if($invoice->reservation->guest->email)
                <p class="text-slate-600">Email: {{ $invoice->reservation->guest->email }}</p>
                @endif
                <p class="text-slate-500 mt-0.5">{{ strtoupper($invoice->reservation->guest->id_type) }}: {{ $invoice->reservation->guest->id_number ?? 'Not Recorded' }}</p>
            </div>

            <div class="sm:text-right space-y-1">
                <span class="font-bold uppercase tracking-wider text-slate-400 text-[10px] block mb-1">Stay Details:</span>
                <p class="font-semibold text-slate-800">
                    Period: {{ $invoice->reservation->check_in_date->format('M d, Y') }} &rarr; {{ $invoice->reservation->check_out_date->format('M d, Y') }}
                </p>
                <p class="text-slate-600">Duration: {{ $invoice->reservation->nights_count }} Night(s)</p>
                <p class="text-slate-600">
                    Room: 
                    @foreach($invoice->reservation->reservationRooms as $rr)
                        <span class="font-bold text-slate-900">{{ $rr->room?->room_number ?? 'TBD' }}</span> ({{ $rr->roomType->name }})
                    @endforeach
                </p>
            </div>
        </div>

        <!-- Itemized Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b-2 border-slate-800 text-slate-800 uppercase font-black text-[10px] tracking-wider">
                        <th class="py-2.5">Item & Description</th>
                        <th class="py-2.5 text-center">Quantity</th>
                        <th class="py-2.5 text-right">Unit Rate (BDT)</th>
                        <th class="py-2.5 text-right">Amount (BDT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($invoice->items as $item)
                    <tr>
                        <td class="py-3 font-semibold text-slate-900">{{ $item->description }}</td>
                        <td class="py-3 text-center">{{ $item->quantity }}</td>
                        <td class="py-3 text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 text-right font-bold text-slate-900">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-slate-400">No folio items on this invoice.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals & Balances -->
        <div class="border-t border-slate-200 pt-4 flex flex-col sm:flex-row justify-between gap-6 text-xs">
            <div class="space-y-1 sm:max-w-xs">
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[10px] block">Payment Terms & Policies:</span>
                <p class="text-slate-500 text-[11px] leading-relaxed">
                    All hospitality bills are payable upon presentation or checkout. Thank you for choosing StayingHotel Hospitality.
                </p>
            </div>

            <div class="sm:min-w-64 space-y-2">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-800">BDT {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>VAT / Hotel Tax ({{ $invoice->branch->tax_rate_percent }}%):</span>
                    <span class="font-bold text-slate-800">BDT {{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>Authorized Discount:</span>
                    <span class="font-bold">- BDT {{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm font-black text-slate-900 border-t border-slate-800 pt-2">
                    <span>Grand Total:</span>
                    <span>BDT {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-bold border-t border-slate-100 pt-1">
                    <span>Total Paid / Settlements:</span>
                    <span>- BDT {{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-black border-t-2 border-slate-800 pt-2 {{ $invoice->balance_due > 0 ? 'text-amber-600' : 'text-emerald-700' }}">
                    <span>Balance Due:</span>
                    <span>BDT {{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Sign-off -->
        <div class="mt-12 pt-6 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
            <div>StayingHotel ERP Official Computer Generated Tax Invoice</div>
            <div>Authorized Financial Representative</div>
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
