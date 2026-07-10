<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([CategorySeeder::class, ShippingZoneSeeder::class, ShippingRateSeeder::class]);

        $users = User::factory(10)
            ->has(Address::factory(), 'addresses')
            ->create();
        
        $products = Product::factory()->count(20)->create();
        
        $time = 0;
        $createdNow = Carbon::now();

        Order::factory(15)
            ->sequence(fn () => ['user_id' => $users->random()->id])
            ->has(OrderItem::factory()
                ->sequence(fn () => ['product_id' => $products->random()->id])
                ->count(fake()->numberBetween(1, 2)), 'items')
            ->create([
            ])
            ->each(function (Order $order) use (&$time, $createdNow) {
                $totalWeight = $order->items->sum(function ($item) {
                    return $item->quantity * $item->product->weight;
                });

                $rate = ShippingRate::where('shipping_zone_id', $order->shipping_zone_id)
                    ->where('min_weight', '<=', $totalWeight)
                    ->where('max_weight', '>=', $totalWeight)
                    ->first()->price;

                $subTotal = $order->items()->sum('subtotal');

                $order->update([
                    'created_at' => $createdNow->copy()->addSeconds($time),
                    'subtotal' => $subTotal,
                    'shipping_cost' => $rate,
                    'total' => $subTotal + $rate,
                ]);

                $time += 1;
            });
    }
}
