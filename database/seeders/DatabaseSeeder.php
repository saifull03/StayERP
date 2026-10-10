<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\CashierTransaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Guest;
use App\Models\GuestFolioItem;
use App\Models\HousekeepingTask;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomAmenity;
use App\Models\RoomType;
use App\Models\User;
use App\Models\UserBranchAssignment;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Organization
        $org = Organization::create([
            'name' => 'StayingHotel Hospitality Group',
            'code' => 'STAY-HQ',
            'contact_email' => 'corporate@stayinghotel.com',
            'contact_phone' => '+880 1711 000000',
            'currency' => 'BDT',
            'timezone' => 'Asia/Dhaka',
            'settings' => [
                'vat_reg_no' => 'BIN-9876543210',
                'website' => 'https://stayinghotel.com',
            ],
        ]);

        // 2. Create Demonstration Branches
        $branchesData = [
            [
                'name' => 'Dhaka Premier Branch',
                'code' => 'DHK',
                'phone' => '+880 1711 111111',
                'email' => 'dhaka@stayinghotel.com',
                'address' => 'Plot 12, Road 45, Gulshan-2',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1212',
                'tax_rate_percent' => 15.00,
            ],
            [
                'name' => 'Chattogram Portside Branch',
                'code' => 'CTG',
                'phone' => '+880 1711 222222',
                'email' => 'chattogram@stayinghotel.com',
                'address' => '88 GEC Circle, Agrabad Commercial Area',
                'city' => 'Chattogram',
                'state' => 'Chattogram',
                'postal_code' => '4000',
                'tax_rate_percent' => 15.00,
            ],
            [
                'name' => "Cox's Bazar Beach Resort",
                'code' => 'CXB',
                'phone' => '+880 1711 333333',
                'email' => 'coxsbazar@stayinghotel.com',
                'address' => 'Kolatoli Marine Drive Beach Front',
                'city' => "Cox's Bazar",
                'state' => 'Chattogram',
                'postal_code' => '4700',
                'tax_rate_percent' => 15.00,
            ],
            [
                'name' => 'Sylhet Tea Valley Branch',
                'code' => 'SYL',
                'phone' => '+880 1711 444444',
                'email' => 'sylhet@stayinghotel.com',
                'address' => 'Airport Road, Dargah Gate',
                'city' => 'Sylhet',
                'state' => 'Sylhet',
                'postal_code' => '3100',
                'tax_rate_percent' => 15.00,
            ],
        ];

        $branches = [];
        foreach ($branchesData as $bData) {
            $branches[$bData['code']] = Branch::create(array_merge($bData, [
                'organization_id' => $org->id,
                'timezone' => 'Asia/Dhaka',
                'currency' => 'BDT',
                'checkin_time' => '14:00:00',
                'checkout_time' => '12:00:00',
                'is_active' => true,
            ]));
        }

        $dhakaBranch = $branches['DHK'];
        $ctgBranch = $branches['CTG'];
        $cxbBranch = $branches['CXB'];
        $sylBranch = $branches['SYL'];

        // 3. Create Permissions
        $permissionsData = [
            // Reservations
            ['name' => 'View Reservations', 'slug' => 'reservations.view', 'module' => 'reservations'],
            ['name' => 'Create Reservations', 'slug' => 'reservations.create', 'module' => 'reservations'],
            ['name' => 'Update Reservations', 'slug' => 'reservations.update', 'module' => 'reservations'],
            ['name' => 'Cancel Reservations', 'slug' => 'reservations.cancel', 'module' => 'reservations'],
            ['name' => 'Check-in Guests', 'slug' => 'reservations.checkin', 'module' => 'reservations'],
            ['name' => 'Check-out Guests', 'slug' => 'reservations.checkout', 'module' => 'reservations'],
            // Rooms
            ['name' => 'View Rooms & Availability', 'slug' => 'rooms.view', 'module' => 'rooms'],
            ['name' => 'Manage Rooms & Types', 'slug' => 'rooms.manage', 'module' => 'rooms'],
            // Guests
            ['name' => 'View Guests', 'slug' => 'guests.view', 'module' => 'guests'],
            ['name' => 'Manage Guests', 'slug' => 'guests.manage', 'module' => 'guests'],
            // Payments & Billing
            ['name' => 'View Payments & Invoices', 'slug' => 'payments.view', 'module' => 'billing'],
            ['name' => 'Collect Payments', 'slug' => 'payments.collect', 'module' => 'billing'],
            ['name' => 'Process Refunds', 'slug' => 'payments.refund', 'module' => 'billing'],
            // Shifts
            ['name' => 'Manage Cashier Shifts', 'slug' => 'shifts.manage', 'module' => 'shifts'],
            ['name' => 'Reconcile Shifts', 'slug' => 'shifts.reconcile', 'module' => 'shifts'],
            // Finance
            ['name' => 'View Financial Reports', 'slug' => 'finance.reports', 'module' => 'finance'],
            ['name' => 'Create Expenses', 'slug' => 'expenses.create', 'module' => 'finance'],
            ['name' => 'Approve Expenses', 'slug' => 'expenses.approve', 'module' => 'finance'],
            // Operations
            ['name' => 'Manage Housekeeping', 'slug' => 'housekeeping.manage', 'module' => 'operations'],
            ['name' => 'Manage Maintenance', 'slug' => 'maintenance.manage', 'module' => 'operations'],
            // Admin & Global
            ['name' => 'Manage Staff', 'slug' => 'staff.manage', 'module' => 'administration'],
            ['name' => 'Manage Branches', 'slug' => 'branches.manage', 'module' => 'administration'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports'],
            ['name' => 'Manage System Settings', 'slug' => 'settings.manage', 'module' => 'administration'],
            ['name' => 'View Audit Logs', 'slug' => 'audit_logs.view', 'module' => 'administration'],
        ];

        $permissionModels = [];
        foreach ($permissionsData as $p) {
            $permissionModels[$p['slug']] = Permission::create($p);
        }

        // 4. Create Roles
        $rolesData = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full unrestricted access across all organizations and branches',
                'is_system' => true,
                'permissions' => array_keys($permissionModels),
            ],
            [
                'name' => 'Branch Manager',
                'slug' => 'branch-manager',
                'description' => 'Full administrative control over authorized hotel branch operations',
                'is_system' => true,
                'permissions' => [
                    'reservations.view', 'reservations.create', 'reservations.update', 'reservations.cancel',
                    'reservations.checkin', 'reservations.checkout', 'rooms.view', 'rooms.manage',
                    'guests.view', 'guests.manage', 'payments.view', 'payments.collect', 'payments.refund',
                    'shifts.manage', 'shifts.reconcile', 'finance.reports', 'expenses.create', 'expenses.approve',
                    'housekeeping.manage', 'maintenance.manage', 'staff.manage', 'reports.export', 'audit_logs.view',
                ],
            ],
            [
                'name' => 'Finance Manager',
                'slug' => 'finance-manager',
                'description' => 'Financial operations, invoices, payment auditing, refunds, and expense management',
                'is_system' => true,
                'permissions' => [
                    'payments.view', 'payments.collect', 'payments.refund', 'shifts.manage', 'shifts.reconcile',
                    'finance.reports', 'expenses.create', 'expenses.approve', 'reports.export', 'audit_logs.view',
                    'reservations.view', 'guests.view',
                ],
            ],
            [
                'name' => 'Cash Counter / Cashier',
                'slug' => 'cashier',
                'description' => 'Counter shift management, receiving deposits and settlements, printing receipts',
                'is_system' => true,
                'permissions' => [
                    'payments.view', 'payments.collect', 'shifts.manage', 'reservations.view', 'guests.view',
                ],
            ],
            [
                'name' => 'Receptionist',
                'slug' => 'receptionist',
                'description' => 'Front desk guest check-in/out, walk-in bookings, room status monitoring',
                'is_system' => true,
                'permissions' => [
                    'reservations.view', 'reservations.create', 'reservations.update', 'reservations.cancel',
                    'reservations.checkin', 'reservations.checkout', 'rooms.view', 'guests.view', 'guests.manage',
                    'payments.view', 'payments.collect',
                ],
            ],
            [
                'name' => 'Housekeeping Staff',
                'slug' => 'housekeeping',
                'description' => 'Room turnover pipeline, cleaning updates, maintenance issue reporting',
                'is_system' => true,
                'permissions' => [
                    'rooms.view', 'housekeeping.manage', 'maintenance.manage',
                ],
            ],
            [
                'name' => 'Maintenance Staff',
                'slug' => 'maintenance',
                'description' => 'Handling repairs, room blocking, maintenance logs, and material tracking',
                'is_system' => true,
                'permissions' => [
                    'rooms.view', 'maintenance.manage',
                ],
            ],
            [
                'name' => 'Inventory Manager',
                'slug' => 'inventory-manager',
                'description' => 'Procurement, vendor coordination, and branch supplies',
                'is_system' => true,
                'permissions' => [
                    'expenses.create', 'finance.reports',
                ],
            ],
            [
                'name' => 'Auditor / Read-only Staff',
                'slug' => 'auditor',
                'description' => 'Read-only access to audit logs, financial reports, and transaction histories',
                'is_system' => true,
                'permissions' => [
                    'reservations.view', 'rooms.view', 'guests.view', 'payments.view', 'finance.reports',
                    'reports.export', 'audit_logs.view',
                ],
            ],
        ];

        $roleModels = [];
        foreach ($rolesData as $r) {
            $perms = $r['permissions'];
            unset($r['permissions']);
            $role = Role::create($r);
            $roleModels[$r['slug']] = $role;

            $permIds = [];
            foreach ($perms as $pSlug) {
                if (isset($permissionModels[$pSlug])) {
                    $permIds[] = $permissionModels[$pSlug]->id;
                }
            }
            $role->permissions()->sync($permIds);
        }

        // 5. Create Staff Users for Demonstration
        $usersData = [
            [
                'name' => 'Executive Super Admin',
                'email' => 'admin@stayinghotel.com',
                'phone' => '+880 1700 000001',
                'designation' => 'Chief Operations Officer',
                'role' => 'super-admin',
                'branch' => null, // Global purview
            ],
            [
                'name' => 'Tanvir Hasan (Dhaka Manager)',
                'email' => 'manager.dhaka@stayinghotel.com',
                'phone' => '+880 1700 000002',
                'designation' => 'General Manager - Dhaka',
                'role' => 'branch-manager',
                'branch' => $dhakaBranch,
            ],
            [
                'name' => 'Nusrat Jahan (Finance Manager)',
                'email' => 'finance@stayinghotel.com',
                'phone' => '+880 1700 000003',
                'designation' => 'Head of Accounts & Finance',
                'role' => 'finance-manager',
                'branch' => $dhakaBranch,
            ],
            [
                'name' => 'Mehedi Hasan (Cashier)',
                'email' => 'cashier.dhaka@stayinghotel.com',
                'phone' => '+880 1700 000004',
                'designation' => 'Senior Cashier',
                'role' => 'cashier',
                'branch' => $dhakaBranch,
            ],
            [
                'name' => 'Sadia Islam (Receptionist)',
                'email' => 'reception.dhaka@stayinghotel.com',
                'phone' => '+880 1700 000005',
                'designation' => 'Front Desk Executive',
                'role' => 'receptionist',
                'branch' => $dhakaBranch,
            ],
            [
                'name' => 'Kamal Hossain (Housekeeping)',
                'email' => 'housekeeping.dhaka@stayinghotel.com',
                'phone' => '+880 1700 000006',
                'designation' => 'Housekeeping Supervisor',
                'role' => 'housekeeping',
                'branch' => $dhakaBranch,
            ],
            [
                'name' => 'Rafiqul Alam (Maintenance)',
                'email' => 'maintenance.dhaka@stayinghotel.com',
                'phone' => '+880 1700 000007',
                'designation' => 'Chief Maintenance Engineer',
                'role' => 'maintenance',
                'branch' => $dhakaBranch,
            ],
            [
                'name' => 'Farhana Akter (Internal Auditor)',
                'email' => 'auditor@stayinghotel.com',
                'phone' => '+880 1700 000008',
                'designation' => 'Internal Compliance Auditor',
                'role' => 'auditor',
                'branch' => $dhakaBranch,
            ],
        ];

        $users = [];
        foreach ($usersData as $u) {
            $user = User::create([
                'organization_id' => $org->id,
                'name' => $u['name'],
                'email' => $u['email'],
                'phone' => $u['phone'],
                'designation' => $u['designation'],
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            $users[$u['email']] = $user;

            UserBranchAssignment::create([
                'user_id' => $user->id,
                'branch_id' => $u['branch']?->id,
                'role_id' => $roleModels[$u['role']]->id,
                'is_primary' => true,
            ]);

            // If super admin or finance, also give access to other branches
            if ($u['role'] === 'super-admin' || $u['role'] === 'finance-manager') {
                foreach ([$ctgBranch, $cxbBranch, $sylBranch] as $b) {
                    UserBranchAssignment::firstOrCreate([
                        'user_id' => $user->id,
                        'branch_id' => $b->id,
                        'role_id' => $roleModels[$u['role']]->id,
                    ], [
                        'is_primary' => false,
                    ]);
                }
            }
        }

        // 6. Room Amenities
        $amenitiesData = [
            ['name' => 'High-Speed Wi-Fi', 'code' => 'wifi', 'icon' => 'wifi'],
            ['name' => 'King Size Bed', 'code' => 'king-bed', 'icon' => 'bed'],
            ['name' => 'Climate Control AC', 'code' => 'ac', 'icon' => 'wind'],
            ['name' => '4K Smart TV', 'code' => 'smart-tv', 'icon' => 'tv'],
            ['name' => 'Mini Bar & Fridge', 'code' => 'mini-bar', 'icon' => 'coffee'],
            ['name' => 'Sea / City View Balcony', 'code' => 'balcony', 'icon' => 'sun'],
            ['name' => 'Private Jacuzzi Bath', 'code' => 'jacuzzi', 'icon' => 'bath'],
            ['name' => 'Electronic Safe', 'code' => 'safe', 'icon' => 'shield'],
        ];

        $amenityModels = [];
        foreach ($amenitiesData as $a) {
            $amenityModels[] = RoomAmenity::create($a);
        }

        // 7. Room Types & Rooms per Branch
        $roomTypesBlueprint = [
            ['name' => 'Deluxe Single', 'slug' => 'deluxe-single', 'price' => 4500.00, 'adults' => 1, 'children' => 0, 'capacity' => 1, 'sqft' => 280],
            ['name' => 'Executive Double', 'slug' => 'executive-double', 'price' => 7500.00, 'adults' => 2, 'children' => 1, 'capacity' => 3, 'sqft' => 420],
            ['name' => 'Family Royal Suite', 'slug' => 'family-royal-suite', 'price' => 12000.00, 'adults' => 4, 'children' => 2, 'capacity' => 6, 'sqft' => 650],
            ['name' => 'Presidential Ocean Suite', 'slug' => 'presidential-ocean-suite', 'price' => 22000.00, 'adults' => 2, 'children' => 2, 'capacity' => 4, 'sqft' => 950],
        ];

        $allRooms = [];
        foreach ($branches as $branch) {
            $roomTypes = [];
            foreach ($roomTypesBlueprint as $blueprint) {
                $rt = RoomType::create([
                    'branch_id' => $branch->id,
                    'name' => $blueprint['name'],
                    'slug' => $blueprint['slug'],
                    'description' => "Luxurious accommodations with modern comforts at {$branch->name}.",
                    'base_price_per_night' => $blueprint['price'],
                    'max_adults' => $blueprint['adults'],
                    'max_children' => $blueprint['children'],
                    'total_capacity' => $blueprint['capacity'],
                    'size_sqft' => $blueprint['sqft'],
                    'is_active' => true,
                ]);
                $rt->amenities()->sync(array_slice(array_column($amenityModels, 'id'), 0, rand(4, 7)));
                $roomTypes[] = $rt;
            }

            // Create physical rooms on 3 floors
            $floors = ['1', '2', '3'];
            foreach ($floors as $floorIndex => $floor) {
                foreach ($roomTypes as $rtIndex => $rt) {
                    $roomNumber = "{$floor}0" . ($rtIndex + 1);
                    $status = 'available';
                    if ($floorIndex === 0 && $rtIndex === 1) {
                        $status = 'cleaning';
                    } elseif ($floorIndex === 2 && $rtIndex === 3) {
                        $status = 'maintenance';
                    }

                    $room = Room::create([
                        'branch_id' => $branch->id,
                        'room_type_id' => $rt->id,
                        'room_number' => $roomNumber,
                        'floor' => $floor,
                        'operational_status' => $status,
                        'maintenance_reason' => $status === 'maintenance' ? 'Annual AC & balcony glass refurbishment' : null,
                        'operational_notes' => 'Well-maintained corner suite with sunset exposure.',
                        'is_active' => true,
                    ]);

                    $allRooms[$branch->code][] = $room;
                }
            }
        }

        // 8. Guests
        $guestsData = [
            [
                'first_name' => 'Mahmudul',
                'last_name' => 'Hasan',
                'email' => 'mahmud.hasan@example.com',
                'phone' => '+880 1812 345678',
                'id_type' => 'nid',
                'id_number' => '1990269250001',
                'address' => 'House 45, Road 7, Dhanmondi',
                'city' => 'Dhaka',
                'vip_status' => true,
                'notes' => 'Frequent business traveler, prefers upper floor quiet rooms.',
            ],
            [
                'first_name' => 'Ayesha',
                'last_name' => 'Sultana',
                'email' => 'ayesha.sultana@example.com',
                'phone' => '+880 1913 456789',
                'id_type' => 'passport',
                'id_number' => 'A08976542',
                'address' => 'Nasirabad Housing Society',
                'city' => 'Chattogram',
                'vip_status' => false,
                'notes' => 'Traveling with family.',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Reynolds',
                'email' => 'david.reynolds@intlcorp.com',
                'phone' => '+880 1614 567890',
                'id_type' => 'passport',
                'id_number' => 'USA99887711',
                'address' => 'Foreign Delegate Mission',
                'city' => 'Dhaka',
                'vip_status' => true,
                'notes' => 'Corporate account billing.',
            ],
            [
                'first_name' => 'Dr. Shahed',
                'last_name' => 'Chowdhury',
                'email' => 'shahed.chowdhury@medicare.bd',
                'phone' => '+880 1715 678901',
                'id_type' => 'nid',
                'id_number' => '1982269250099',
                'address' => 'Shahjalal Upashohor',
                'city' => 'Sylhet',
                'vip_status' => false,
                'notes' => 'Attending national medical convention.',
            ],
        ];

        $guests = [];
        foreach ($guestsData as $g) {
            $guests[] = Guest::create(array_merge($g, [
                'organization_id' => $org->id,
                'country' => 'Bangladesh',
            ]));
        }

        // 9. Cashier Shift (Dhaka Branch - Open Shift for Mehedi Cashier)
        $cashierUser = $users['cashier.dhaka@stayinghotel.com'];
        $dhakaShift = CashierShift::create([
            'branch_id' => $dhakaBranch->id,
            'user_id' => $cashierUser->id,
            'shift_number' => CashierShift::generateShiftNumber($dhakaBranch->id),
            'opening_cash' => 15000.00,
            'status' => 'open',
            'opened_at' => Carbon::now()->subHours(5),
            'notes' => 'Morning front desk cashier shift opened with standard cash float.',
        ]);

        // 10. Reservations, Invoices, Folios & Payments
        $today = Carbon::today();

        // Reservation 1: Currently Checked In (Mahmudul Hasan @ Dhaka)
        $dhakaRoom1 = $allRooms['DHK'][0]; // Room 101 Deluxe Single
        $res1 = Reservation::create([
            'branch_id' => $dhakaBranch->id,
            'guest_id' => $guests[0]->id,
            'reservation_number' => Reservation::generateReservationNumber($dhakaBranch->id),
            'check_in_date' => $today->copy()->subDays(1),
            'check_out_date' => $today->copy()->addDays(2),
            'status' => 'checked_in',
            'source' => 'direct',
            'adults' => 1,
            'children' => 0,
            'special_requests' => 'Late check-out requested.',
            'checked_in_at' => $today->copy()->subDays(1)->setTime(14, 30),
            'created_by_user_id' => $users['reception.dhaka@stayinghotel.com']->id,
        ]);

        $res1Room = ReservationRoom::create([
            'reservation_id' => $res1->id,
            'room_id' => $dhakaRoom1->id,
            'room_type_id' => $dhakaRoom1->room_type_id,
            'nightly_rate' => $dhakaRoom1->roomType->base_price_per_night,
            'nights_count' => 3,
            'total_amount' => $dhakaRoom1->roomType->base_price_per_night * 3,
        ]);

        // Room folio item
        GuestFolioItem::create([
            'reservation_id' => $res1->id,
            'branch_id' => $dhakaBranch->id,
            'item_type' => 'room_charge',
            'description' => "Room {$dhakaRoom1->room_number} (3 nights @ BDT {$dhakaRoom1->roomType->base_price_per_night})",
            'quantity' => 1,
            'unit_price' => $res1Room->total_amount,
            'total_amount' => $res1Room->total_amount,
            'added_by_user_id' => $users['reception.dhaka@stayinghotel.com']->id,
        ]);

        // Service Folio item (Dining)
        GuestFolioItem::create([
            'reservation_id' => $res1->id,
            'branch_id' => $dhakaBranch->id,
            'item_type' => 'restaurant',
            'description' => 'A la carte Dinner Room Service',
            'quantity' => 1,
            'unit_price' => 1850.00,
            'total_amount' => 1850.00,
            'added_by_user_id' => $users['reception.dhaka@stayinghotel.com']->id,
        ]);

        // Invoice for Res 1
        $inv1 = Invoice::create([
            'branch_id' => $dhakaBranch->id,
            'reservation_id' => $res1->id,
            'invoice_number' => Invoice::generateInvoiceNumber($dhakaBranch->id),
            'subtotal' => $res1Room->total_amount + 1850.00,
            'tax_amount' => ($res1Room->total_amount + 1850.00) * 0.15,
            'discount_amount' => 500.00,
            'grand_total' => ($res1Room->total_amount + 1850.00) * 1.15 - 500.00,
            'paid_amount' => 10000.00,
            'balance_due' => (($res1Room->total_amount + 1850.00) * 1.15 - 500.00) - 10000.00,
            'status' => 'partially_paid',
            'issued_date' => $today->copy()->subDays(1),
            'due_date' => $today->copy()->addDays(2),
            'notes' => 'Deposit collected upon check-in.',
            'created_by_user_id' => $users['reception.dhaka@stayinghotel.com']->id,
        ]);

        // Payment 1 for Res 1 (Cash deposit collected in shift)
        $pay1 = Payment::create([
            'branch_id' => $dhakaBranch->id,
            'reservation_id' => $res1->id,
            'invoice_id' => $inv1->id,
            'cashier_shift_id' => $dhakaShift->id,
            'payment_number' => Payment::generatePaymentNumber($dhakaBranch->id),
            'amount' => 10000.00,
            'payment_method' => 'cash',
            'transaction_reference' => 'CASH-DEP-01',
            'status' => 'completed',
            'notes' => 'Check-in cash advance deposit',
            'received_by_user_id' => $cashierUser->id,
            'paid_at' => Carbon::now()->subHours(4),
        ]);

        CashierTransaction::create([
            'cashier_shift_id' => $dhakaShift->id,
            'branch_id' => $dhakaBranch->id,
            'transaction_type' => 'payment_received',
            'amount' => 10000.00,
            'payment_id' => $pay1->id,
            'reference_number' => $pay1->payment_number,
            'notes' => 'Advance cash payment for Res ' . $res1->reservation_number,
        ]);

        // Reservation 2: Confirmed arrival today (David Reynolds @ Dhaka)
        $dhakaRoom2 = $allRooms['DHK'][2]; // Room 201
        $res2 = Reservation::create([
            'branch_id' => $dhakaBranch->id,
            'guest_id' => $guests[2]->id,
            'reservation_number' => Reservation::generateReservationNumber($dhakaBranch->id),
            'check_in_date' => $today,
            'check_out_date' => $today->copy()->addDays(3),
            'status' => 'confirmed',
            'source' => 'online',
            'adults' => 2,
            'children' => 0,
            'special_requests' => 'Airport pickup confirmed for 3:00 PM.',
            'created_by_user_id' => $users['reception.dhaka@stayinghotel.com']->id,
        ]);

        ReservationRoom::create([
            'reservation_id' => $res2->id,
            'room_id' => $dhakaRoom2->id,
            'room_type_id' => $dhakaRoom2->room_type_id,
            'nightly_rate' => $dhakaRoom2->roomType->base_price_per_night,
            'nights_count' => 3,
            'total_amount' => $dhakaRoom2->roomType->base_price_per_night * 3,
        ]);

        // Reservation 3: Checked Out Yesterday (Ayesha Sultana @ Cox's Bazar)
        $cxbRoom = $allRooms['CXB'][0];
        $res3 = Reservation::create([
            'branch_id' => $cxbBranch->id,
            'guest_id' => $guests[1]->id,
            'reservation_number' => Reservation::generateReservationNumber($cxbBranch->id),
            'check_in_date' => $today->copy()->subDays(3),
            'check_out_date' => $today->copy()->subDays(1),
            'status' => 'checked_out',
            'source' => 'direct',
            'adults' => 2,
            'children' => 1,
            'checked_in_at' => $today->copy()->subDays(3)->setTime(14, 0),
            'checked_out_at' => $today->copy()->subDays(1)->setTime(11, 45),
            'created_by_user_id' => $users['admin@stayinghotel.com']->id,
        ]);

        $res3Room = ReservationRoom::create([
            'reservation_id' => $res3->id,
            'room_id' => $cxbRoom->id,
            'room_type_id' => $cxbRoom->room_type_id,
            'nightly_rate' => $cxbRoom->roomType->base_price_per_night,
            'nights_count' => 2,
            'total_amount' => $cxbRoom->roomType->base_price_per_night * 2,
        ]);

        $inv3 = Invoice::create([
            'branch_id' => $cxbBranch->id,
            'reservation_id' => $res3->id,
            'invoice_number' => Invoice::generateInvoiceNumber($cxbBranch->id),
            'subtotal' => $res3Room->total_amount,
            'tax_amount' => $res3Room->total_amount * 0.15,
            'discount_amount' => 0.00,
            'grand_total' => $res3Room->total_amount * 1.15,
            'paid_amount' => $res3Room->total_amount * 1.15,
            'balance_due' => 0.00,
            'status' => 'paid',
            'issued_date' => $today->copy()->subDays(1),
            'due_date' => $today->copy()->subDays(1),
            'notes' => 'Settled via credit card upon departure.',
            'created_by_user_id' => $users['admin@stayinghotel.com']->id,
        ]);

        Payment::create([
            'branch_id' => $cxbBranch->id,
            'reservation_id' => $res3->id,
            'invoice_id' => $inv3->id,
            'payment_number' => Payment::generatePaymentNumber($cxbBranch->id),
            'amount' => $inv3->grand_total,
            'payment_method' => 'card',
            'transaction_reference' => 'VISA-AUTH-991283',
            'status' => 'completed',
            'notes' => 'Settled with City Bank Visa Card',
            'received_by_user_id' => $users['admin@stayinghotel.com']->id,
            'paid_at' => $today->copy()->subDays(1)->setTime(11, 30),
        ]);

        // 11. Vendors & Expense Categories
        $expCategories = [
            ['name' => 'Housekeeping Supplies', 'code' => 'EXP-HK', 'desc' => 'Detergents, linens, bath amenities'],
            ['name' => 'Utilities & Power', 'code' => 'EXP-UTIL', 'desc' => 'DESCO electricity, gas, water supply'],
            ['name' => 'Maintenance & Repairs', 'code' => 'EXP-MAINT', 'desc' => 'HVAC, plumbing, electrical spares'],
            ['name' => 'Food & Beverage Provisions', 'code' => 'EXP-FB', 'desc' => 'Restaurant groceries, beverage stock'],
            ['name' => 'Stationery & Front Desk', 'code' => 'EXP-STAT', 'desc' => 'Keycards, folios, office stationery'],
        ];

        $expCatModels = [];
        foreach ($expCategories as $ec) {
            $expCatModels[] = ExpenseCategory::create([
                'branch_id' => $dhakaBranch->id,
                'name' => $ec['name'],
                'code' => $ec['code'],
                'description' => $ec['desc'],
                'is_active' => true,
            ]);
        }

        $vendor = Vendor::create([
            'branch_id' => $dhakaBranch->id,
            'name' => 'CleanPro Hotel Solutions Ltd.',
            'company_name' => 'CleanPro BD',
            'phone' => '+880 1819 888777',
            'email' => 'sales@cleanpro.com.bd',
            'address' => 'Tejgaon Industrial Area, Dhaka',
            'is_active' => true,
        ]);

        // Branch Expense
        Expense::create([
            'branch_id' => $dhakaBranch->id,
            'expense_category_id' => $expCatModels[0]->id,
            'vendor_id' => $vendor->id,
            'cashier_shift_id' => null,
            'expense_number' => Expense::generateExpenseNumber($dhakaBranch->id),
            'title' => 'Bulk Premium Bath Towels & Toiletries Restock',
            'description' => 'Delivery of 50 luxury bath towels and 200 hygiene amenity sets.',
            'amount' => 34500.00,
            'payment_method' => 'bank_transfer',
            'expense_date' => $today->copy()->subDays(2),
            'status' => 'approved',
            'requested_by_user_id' => $users['housekeeping.dhaka@stayinghotel.com']->id,
            'approved_by_user_id' => $users['finance@stayinghotel.com']->id,
        ]);

        // 12. Housekeeping Task
        HousekeepingTask::create([
            'branch_id' => $dhakaBranch->id,
            'room_id' => $allRooms['DHK'][1]->id, // Room 102
            'assigned_to_user_id' => $users['housekeeping.dhaka@stayinghotel.com']->id,
            'priority' => 'high',
            'status' => 'cleaning',
            'cleaning_type' => 'checkout',
            'scheduled_for' => $today,
            'started_at' => Carbon::now()->subMinutes(45),
            'notes' => 'Deep sanitize and replenish VIP minibar provisions.',
        ]);

        // 13. Maintenance Request
        MaintenanceRequest::create([
            'branch_id' => $dhakaBranch->id,
            'room_id' => $allRooms['DHK'][3]->id, // Room 202
            'reported_by_user_id' => $users['reception.dhaka@stayinghotel.com']->id,
            'assigned_to_user_id' => $users['maintenance.dhaka@stayinghotel.com']->id,
            'title' => 'Master Bathroom Water Pressure Fluctuation',
            'description' => 'Thermostatic shower valve requires recalibration before next check-in.',
            'priority' => 'medium',
            'status' => 'in_progress',
            'blocks_room' => false,
            'estimated_cost' => 1500.00,
        ]);

        // 14. Notifications
        Notification::create([
            'user_id' => $users['manager.dhaka@stayinghotel.com']->id,
            'branch_id' => $dhakaBranch->id,
            'title' => 'VIP Arrival Scheduled Today',
            'message' => "Guest {$guests[2]->full_name} is arriving today at Dhaka Premier Branch.",
            'type' => 'info',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $users['finance@stayinghotel.com']->id,
            'branch_id' => $dhakaBranch->id,
            'title' => 'Cash Float Shift Opened',
            'message' => "Cashier Mehedi Hasan initiated morning shift {$dhakaShift->shift_number}.",
            'type' => 'success',
            'is_read' => true,
            'read_at' => Carbon::now()->subHours(4),
        ]);

        // 15. Audit Logs
        AuditLog::create([
            'organization_id' => $org->id,
            'branch_id' => $dhakaBranch->id,
            'user_id' => $users['admin@stayinghotel.com']->id,
            'action' => 'branch.created',
            'target_type' => Branch::class,
            'target_id' => $dhakaBranch->id,
            'new_values' => ['name' => $dhakaBranch->name, 'code' => $dhakaBranch->code],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'StayingHotel-Core/1.0',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        AuditLog::create([
            'organization_id' => $org->id,
            'branch_id' => $dhakaBranch->id,
            'user_id' => $cashierUser->id,
            'action' => 'payment.collected',
            'target_type' => Payment::class,
            'target_id' => $pay1->id,
            'new_values' => ['amount' => 10000.00, 'method' => 'cash'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'StayingHotel-Core/1.0',
            'created_at' => Carbon::now()->subHours(4),
        ]);
    }
}
