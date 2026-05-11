<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display the landing page with products and user orders.
     */
    public function index()
    {
        $products = Product::all();
        $orders = Auth::check()
            ? Order::with('orderItems.product')->where('user_id', Auth::id())->latest()->get()
            : collect();

        return view('welcome', compact('products', 'orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'salutation' => 'required|string',
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string',
            'email'      => 'required|email',
            'cart_data'  => 'required|string',
        ]);

        $cartItems = json_decode($validated['cart_data'], true);

        if (empty($cartItems)) {
            return back()->with('error', 'Your cart is empty.');
        }

        try {
            $orderData = DB::transaction(function () use ($validated, $cartItems) {
                $user = User::firstOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name'     => $validated['name'],
                        'password' => Hash::make(Str::random(12)),
                    ]
                );

                $customer = Customer::firstOrCreate(
                    ['email' => $validated['email']],
                    [
                        'salutation' => $validated['salutation'],
                        'name'       => $validated['name'],
                        'phone'      => $validated['phone'],
                    ]
                );

                $totalAmount = 0;
                $processedItems = [];

                foreach ($cartItems as $item) {
                    $product = Product::findOrFail($item['id']);
                    $quantity = max(1, intval($item['quantity']));
                    $lineTotal = $product->price * $quantity;

                    $totalAmount += $lineTotal;

                    $processedItems[] = [
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'price'      => $product->price,
                    ];
                }

                $order = Order::create([
                    'user_id'      => $user->id,
                    'customer_id'  => $customer->id,
                    'status'       => 'pending',
                    'total_amount' => $totalAmount,
                    'paid_amount'  => 0,
                ]);

                foreach ($processedItems as $pItem) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $pItem['product_id'],
                        'quantity'   => $pItem['quantity'],
                        'price'      => $pItem['price'],
                    ]);
                }

                return ['order' => $order, 'user' => $user];
            });

            Auth::login($orderData['user']);

            return redirect()->route('welcome')->with('success', "Order #{$orderData['order']->id} placed successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
