<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | StayingHotel ERP</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        h1, h2, h3, .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased text-slate-800" x-data="{ sidebarOpen: false, userDropdown: false }">
    <div class="min-h-full flex flex-col lg:flex-row">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden transition-opacity">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-200 transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col shrink-0 border-r border-slate-800">
            
            <!-- Branding Header -->
            <div class="h-18 flex items-center justify-between px-6 border-b border-slate-800/80 bg-slate-950/40">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-900/30">
                        <i data-lucide="hotel" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold tracking-tight text-white block leading-tight font-heading">StayingHotel</span>
                        <span class="text-[11px] font-semibold text-emerald-400 tracking-wider uppercase block">ERP Business Core</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-md">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Active User / Primary Branch Badge -->
            <div class="p-4 mx-4 my-3 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 text-slate-200 flex items-center justify-center font-bold text-xs uppercase border border-slate-600">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-emerald-400 font-medium truncate">{{ auth()->user()->getPrimaryRole()?->name ?? 'Staff' }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-2 space-y-1.5 overflow-y-auto">
                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 px-3 pt-2 pb-1">Operations</div>

                <!-- Unified/Role Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard*') || request()->routeIs('home') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('branch-manager'))
                <!-- Direct Switcher to other Role Dashboards for Testing & Overview -->
                <div x-data="{ openRoleViews: false }">
                    <button @click="openRoleViews = !openRoleViews" class="w-full flex items-center justify-between px-3.5 py-2 rounded-lg text-xs font-medium text-slate-400 hover:bg-slate-800/60 hover:text-slate-200 transition-colors">
                        <span class="flex items-center gap-2">
                            <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                            <span>Switch Role View</span>
                        </span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="openRoleViews ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openRoleViews" x-cloak class="pl-6 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('dashboard.super-admin') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Super Admin View</a>
                        <a href="{{ route('dashboard.branch-manager') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Branch Manager View</a>
                        <a href="{{ route('dashboard.finance') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Finance Manager View</a>
                        <a href="{{ route('dashboard.cashier') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Cash Counter View</a>
                        <a href="{{ route('dashboard.receptionist') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Receptionist View</a>
                        <a href="{{ route('dashboard.housekeeping') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Housekeeping View</a>
                        <a href="{{ route('dashboard.maintenance') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Maintenance View</a>
                        <a href="{{ route('dashboard.auditor') }}" class="block py-1 text-slate-400 hover:text-emerald-400">Auditor View</a>
                    </div>
                </div>
                @endif

                @if(auth()->user()->hasPermission('reservations.view'))
                <a href="{{ route('reservations.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reservations.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    <span>Reservations & Stays</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('rooms.view'))
                <a href="{{ route('rooms.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('rooms.index') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="bed-double" class="w-4 h-4"></i>
                    <span>Rooms & Inventory</span>
                </a>
                <a href="{{ route('rooms.calendar') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('rooms.calendar') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    <span>Availability Grid</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('guests.view'))
                <a href="{{ route('guests.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('guests.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Guest Directory</span>
                </a>
                @endif

                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 px-3 pt-4 pb-1">Finance & Counters</div>

                @if(auth()->user()->hasPermission('payments.view'))
                <a href="{{ route('billing.invoices') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('billing.invoices*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Guest Invoices</span>
                </a>
                <a href="{{ route('billing.payments') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('billing.payments*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    <span>Payment Ledger</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('shifts.manage'))
                <a href="{{ route('shifts.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('shifts.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span>Cashier Shifts</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('finance.reports') || auth()->user()->hasPermission('expenses.create'))
                <a href="{{ route('expenses.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('expenses.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="coins" class="w-4 h-4"></i>
                    <span>Branch Expenses</span>
                </a>
                @endif

                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 px-3 pt-4 pb-1">Facility Operations</div>

                @if(auth()->user()->hasPermission('housekeeping.manage'))
                <a href="{{ route('housekeeping.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('housekeeping.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Housekeeping</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('maintenance.manage'))
                <a href="{{ route('maintenance.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('maintenance.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                    <span>Maintenance Board</span>
                </a>
                @endif

                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 px-3 pt-4 pb-1">Governance</div>

                @if(auth()->user()->hasPermission('finance.reports'))
                <a href="{{ route('reports.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span>Reports & Analytics</span>
                </a>
                @endif

                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('branches.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('branches.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="network" class="w-4 h-4"></i>
                    <span>Branch Management</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('staff.manage'))
                <a href="{{ route('staff.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('staff.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                    <span>Staff & Roles</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('audit_logs.view'))
                <a href="{{ route('audit-logs.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('audit-logs.*') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    <span>Audit Logs</span>
                </a>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-800 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-rose-300 hover:bg-rose-950/40 hover:text-rose-200 border border-rose-900/40 transition-colors">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Sign Out of StayingHotel</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="h-18 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 font-heading">@yield('page-header', 'StayingHotel ERP')</h1>
                        <p class="text-xs text-slate-500 hidden sm:block">Multi-Branch Hotel Operations & Management Suite</p>
                    </div>
                </div>

                <!-- Top Bar Actions (Branch Switcher, Notifications, User) -->
                <div class="flex items-center gap-3 sm:gap-4">
                    
                    <!-- Dynamic Branch Switcher -->
                    @if(isset($authorizedBranches) && $authorizedBranches->isNotEmpty())
                    <form method="POST" action="{{ route('branch.switch') }}" class="flex items-center">
                        @csrf
                        <div class="relative flex items-center bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-xs hover:border-slate-400 transition-colors">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 mr-2 shrink-0"></i>
                            <select name="branch_id" onchange="this.form.submit()" class="bg-transparent border-none focus:ring-0 text-xs font-semibold text-slate-800 pr-6 cursor-pointer outline-hidden">
                                @if(auth()->user()->isSuperAdmin())
                                    <option value="all" {{ session('active_branch_id') === 'all' ? 'selected' : '' }}>🏢 All Branches (Consolidated)</option>
                                @endif
                                @foreach($authorizedBranches as $b)
                                    <option value="{{ $b->id }}" {{ session('active_branch_id') == $b->id ? 'selected' : '' }}>
                                        📍 {{ $b->name }} ({{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    @endif

                    <!-- Notifications Dropdown / Link -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                        @endif
                    </a>

                    <!-- User Pill -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="text-xs font-semibold text-slate-700 hidden md:block">{{ auth()->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <div x-show="open" 
                             x-cloak 
                             @click.away="open = false" 
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-bold text-slate-900">{{ auth()->user()->name }}</p>
                                <p class="text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('notifications.index') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50">
                                <i data-lucide="bell" class="w-3.5 h-3.5 text-slate-400"></i>
                                Notifications
                            </a>
                            <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50">
                                <i data-lucide="activity" class="w-3.5 h-3.5 text-slate-400"></i>
                                Activity History
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-rose-600 hover:bg-rose-50 font-medium">
                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alerts & Flash Messages -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200/80 p-4 text-emerald-800 flex items-center gap-3 shadow-xs">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200/80 p-4 text-rose-800 flex items-center gap-3 shadow-xs">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200/80 p-4 text-amber-900 shadow-xs">
                    <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                        <span>Validation errors occurred:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-amber-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <!-- Page Body -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-12 overflow-y-auto">
                @yield('content')
            </main>

            <!-- App Footer -->
            <footer class="bg-white border-t border-slate-200/80 px-4 sm:px-6 lg:px-8 py-3 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div>
                    <span class="font-bold text-slate-700">StayingHotel ERP</span> — Enterprise Multi-Branch Hospitality Management
                </div>
                <div class="text-[11px] text-slate-400">
                    Active Context: <span class="font-semibold text-slate-600">{{ $activeBranch?->name ?? 'Consolidated (All Branches)' }}</span>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.createIcons && window.lucideIcons) {
                window.createIcons({ icons: window.lucideIcons });
            }
        });
    </script>
</body>
</html>
