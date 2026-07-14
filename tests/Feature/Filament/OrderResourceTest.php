<?php

/**
 * Test ini menguji alur pengelolaan pesanan di Filament admin panel (Resource
 * List/View/Edit + Action + Filter), melengkapi tests/Feature/OrderModelTest.php
 * yang sudah ada (yang hanya menguji model secara langsung).
 *
 * Pemetaan terhadap skenario QA:
 * - Skenario 1 (Alur normal / happy path)   -> describe('Skenario 1 ...')
 * - Skenario 2 (Pembatalan pesanan)         -> describe('Skenario 2 ...')
 * - Skenario 3 (Pengujian filter)           -> describe('Skenario 3 ...')
 *
 * CATATAN KETERBATASAN:
 * Pest + Livewire testing tidak menjalankan browser sungguhan, sehingga hal-hal
 * yang sifatnya murni visual (warna modal yang benar-benar dirender, animasi
 * progress bar) tidak bisa diverifikasi pixel-by-pixel di sini. Yang diuji
 * adalah representasi datanya di level komponen: konfigurasi Action (warna,
 * visible/hidden), perubahan data (status, riwayat status, tracking number),
 * dan hasil query tabel (filter). Untuk verifikasi visual murni sebaiknya
 * dilengkapi dengan Laravel Dusk atau pengujian manual/E2E terpisah.
 *
 * CATATAN LAIN:
 * - Test "dapat mengubah status dari shipped ke delivered ... nomor resi tetap
 *   tersimpan" dibuat untuk memverifikasi temuan #2 di improve.md. Berdasarkan
 *   pembacaan kode saat ini, test ini KEMUNGKINAN BESAR GAGAL sampai bug
 *   tersebut diperbaiki di OrderForm.php — ini disengaja sebagai regression
 *   test, bukan kesalahan penulisan test.
 * - Autentikasi admin dibuat manual (bukan Admin::factory()) karena file
 *   AdminFactory tidak ada di dalam PR ini. Sesuaikan guard name ('admin')
 *   bila konfigurasi panel di project berbeda.
 * - Jika `callAction('update_status', ...)` tidak ditemukan oleh Livewire
 *   testing (mis. karena versi Filament berbeda menangani action di dalam
 *   Section->headerActions() secara berbeda), gunakan pola alternatif:
 *   ->mountAction('update_status')->setActionData([...])->callMountedAction()
 */

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Admin;
use App\Models\Order;
use App\OrderStatus;
use App\PaymentMethod;
use Database\Seeders\OrderSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Data dasar: 15 order (semua berstatus Pending di awal, sesuai OrderFactory),
    // lengkap dengan user, address, product, shipping zone & rate.
    $this->seed([OrderSeeder::class]);

    // Admin yang login ke Filament panel (guard 'admin', sesuai id panel di
    // AdminPanelProvider). Dibuat manual karena tidak ada AdminFactory di PR ini.
    $this->admin = Admin::query()->create([
        'name' => 'QA Tester',
        'email' => 'qa.tester@dimostore.test',
        'role' => 'admin',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($this->admin, 'admin');
});

// -----------------------------------------------------------------------
// Skenario 1 — Alur normal (happy path)
// -----------------------------------------------------------------------
describe('Scenario 1 - Normal flow (happy path)', function () {

    it('display orders with a "pending" status on the order list page', function () {
        $pendingOrder = Order::where('status', OrderStatus::Pending)->first();

        expect($pendingOrder)->not()->toBeNull();

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords([$pendingOrder]);
    });

    it('display the order details page with the correct information', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($order->order_number)
            ->assertSee($order->user->name)
            ->assertSee($order->user->email)
            ->assertSee($order->recipient_name)
            ->assertSee($order->recipient_phone);
    });

    it('can change the status from "pending" to "processing" along with a note', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionExists(
                TestAction::make('update_status')
                    ->schemaComponent('s-timeline-status')
            )
            ->callAction(
                TestAction::make('update_status')
                    ->schemaComponent('s-timeline-status'),
                data: [
                    'changelog' => 'Being packed'
                ]
            )
            ->assertHasNoFormErrors()
            ->assertNotified();

        $order->refresh();

        expect($order->status)->toBe(OrderStatus::Processing);

        $history = $order->statusHistories()->latest('id')->first();
        expect($history)->not()->toBeNull();
        expect($history->status)->toBe(OrderStatus::Processing);
        expect($history->note)->toBe('Being packed');
    });

    it('can change the status from processing to shipped along with the receipt number', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();
        $order->update(['status' => OrderStatus::Processing]);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->callAction(
                TestAction::make('update_status')
                    ->schemaComponent('s-timeline-status'),
                data: [
                    'tracking_number' => 'JNE0001',
                    'changelog' => null,
                ]
            )
            ->assertHasNoFormErrors()
            ->assertNotified();

        $order->refresh();

        expect($order->status)->toBe(OrderStatus::Shipped);
        expect($order->tracking_number)->toBe('JNE0001');

        $history = $order->statusHistories()->latest('id')->first();
        expect($history->status)->toBe(OrderStatus::Shipped);
    });

    it('the tracking number remains stored when the status changes from shipped to delivered.', function () {
        // Regression test untuk temuan #2 di improve.md.
        $order = Order::where('status', OrderStatus::Pending)->first();
        $order->update([
            'status' => OrderStatus::Shipped,
            'tracking_number' => 'JNE0001',
        ]);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->callAction(
                TestAction::make('update_status')
                    ->schemaComponent('s-timeline-status'),
            )
            ->assertHasNoFormErrors();

        $order->refresh();

        expect($order->status)->toBe(OrderStatus::Delivered);

        // Lihat improve.md temuan #2: pada kode saat ini nomor resi berisiko
        // ter-overwrite menjadi null pada transisi ini.
        expect($order->tracking_number)->toBe('JNE0001');
    });

    it('disable status update action after order delivered', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();
        $order->update([
            'status' => OrderStatus::Delivered,
            'tracking_number' => 'JNE0001',
        ]);

        $order->refresh();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertSchemaComponentExists('s-timeline-status')
            ->assertActionNotMounted(
                TestAction::make('update_status')
                    ->schemaComponent('s-timeline-status'),
            );

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('edit');
    });
});


describe('Scenario 2 - order cancellation', function () {

    it('displays the cancellation action in danger color on pending orders', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionVisible('cancel_btn')
            ->assertActionHasColor('cancel_btn', 'danger');
    });

    it('can cancel pending orders along with a note of the reason', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('cancel_btn', data: [
                'changelog' => 'No stock',
            ])
            ->assertHasNoFormErrors()
            ->assertNotified();

        $order->refresh();

        expect($order->status)->toBe(OrderStatus::Canceled);

        $history = $order->statusHistories()
            ->where('status', OrderStatus::Canceled)
            ->latest('id')
            ->first();

        expect($history)->not()->toBeNull();
        expect($history->note)->toBe('No stock');
    });

    it('disable status update and cancellation actions after order is cancelled', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();
        $order->update(['status' => OrderStatus::Canceled]);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionNotMounted('update_status')
            ->assertActionNotMounted('cancel_btn');

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('edit');
    });

    it('canceled orders still appear in the order list with the status canceled', function () {
        $order = Order::where('status', OrderStatus::Pending)->first();
        $order->update(['status' => OrderStatus::Canceled]);

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords([$order]);

        expect($order->fresh()->status)->toBe(OrderStatus::Canceled);
    });
});

describe('Scenario 3 - filter test', function () {

    beforeEach(function () {
        Order::query()
            ->orderBy('id')
            ->take(5)
            ->get()
            ->each(fn (Order $order) => $order->update(['status' => OrderStatus::Processing]));
    });

    it('displays only orders with pending status when status filter is applied', function () {
        $pendingOrders = Order::where('status', OrderStatus::Pending)->get();
        $otherOrders = Order::where('status', '!=', OrderStatus::Pending->value)->get();

        expect($pendingOrders)->not()->toBeEmpty();
        expect($otherOrders)->not()->toBeEmpty();

        Livewire::test(ListOrders::class)
            ->filterTable('status', ['pending'])
            ->assertCanSeeTableRecords($pendingOrders)
            ->assertCanNotSeeTableRecords($otherOrders);
    });

    it('apply status and payment method filters simultaneously', function () {
        $target = Order::where('status', OrderStatus::Pending)->first();
        $target->update(['payment_method' => PaymentMethod::Bank]);

        $matching = Order::where('status', OrderStatus::Pending)
            ->where('payment_method', PaymentMethod::Bank->value)
            ->get();

        $nonMatching = Order::where('status', '!=', OrderStatus::Pending->value)
            ->orWhere('payment_method', '!=', PaymentMethod::Bank->value)
            ->get();

        expect($matching)->not()->toBeEmpty();

        Livewire::test(ListOrders::class)
            ->filterTable('status', ['pending'])
            ->filterTable('payment_method', ['bank_transfer'])
            ->assertCanSeeTableRecords($matching)
            ->assertCanNotSeeTableRecords($nonMatching);
    });

    it('redisplay all orders after filter reset', function () {

        $allOrders = Order::all();

        Livewire::test(ListOrders::class)
            ->filterTable('status', ['pending'])
            ->filterTable('payment_method', ['bank_transfer'])
            ->resetTableFilters()
            ->assertCountTableRecords($allOrders->count())
            ->assertCanSeeTableRecords(Order::orderByDesc('created_at')->take(10)->get());
    });
});