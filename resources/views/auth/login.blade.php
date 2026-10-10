<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | StayingHotel ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-900 text-slate-800" x-data="{
    fillDemo(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }
}">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white shadow-xl shadow-emerald-950/50 mb-4">
            <i data-lucide="hotel" class="w-8 h-8"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight font-heading">StayingHotel ERP</h1>
        <p class="mt-2 text-sm text-slate-400">Enterprise Multi-Branch Hotel Management Portal</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-2xl rounded-2xl sm:px-10 border border-slate-200/80">
            
            @if(session('error'))
            <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                @foreach($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
            @endif

            <form class="space-y-5" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Work Email Address</label>
                    <div class="mt-1.5 relative rounded-lg shadow-2xs">
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               value="{{ old('email', 'admin@stayinghotel.com') }}"
                               placeholder="e.g. admin@stayinghotel.com"
                               class="block w-full rounded-lg border border-slate-300 py-2.5 px-3 text-sm placeholder:text-slate-400 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                    <div class="mt-1.5 relative rounded-lg shadow-2xs">
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               value="password"
                               placeholder="••••••••"
                               class="block w-full rounded-lg border border-slate-300 py-2.5 px-3 text-sm placeholder:text-slate-400 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-hidden">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="remember" class="ml-2 block text-xs font-medium text-slate-600">Keep me authenticated</label>
                    </div>
                    <span class="text-xs text-slate-400">Default password: <code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-slate-700">password</code></span>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        Sign In to StayingHotel ERP
                    </button>
                </div>
            </form>

            <!-- Quick Demo One-Click Fillers -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 text-center mb-3">One-Click Demonstration Logins</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                    <button type="button" @click="fillDemo('admin@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        👑 Super Admin
                    </button>
                    <button type="button" @click="fillDemo('manager.dhaka@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        🏢 Branch Manager
                    </button>
                    <button type="button" @click="fillDemo('finance@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        💰 Finance Manager
                    </button>
                    <button type="button" @click="fillDemo('cashier.dhaka@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        💵 Cash Counter
                    </button>
                    <button type="button" @click="fillDemo('reception.dhaka@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        🛎️ Receptionist
                    </button>
                    <button type="button" @click="fillDemo('housekeeping.dhaka@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        ✨ Housekeeping
                    </button>
                    <button type="button" @click="fillDemo('maintenance.dhaka@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        🔧 Maintenance
                    </button>
                    <button type="button" @click="fillDemo('auditor@stayinghotel.com')" class="p-2 rounded-lg border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-700 font-semibold transition-colors">
                        📋 Auditor
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            StayingHotel ERP &copy; {{ date('Y') }}. All rights reserved.
        </p>
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
