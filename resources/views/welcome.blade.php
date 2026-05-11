<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartCaffee - Freshly Brewed Excellence</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Custom scrollbar for the cart */
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #ea580c;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-[#FDFBF9] antialiased text-slate-900"
    x-data="{ 
        openModal: false, 
        cart: [],
        scrolled: false,
        addToCart(product) {
            let existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({ ...product, quantity: 1 });
            }
        },
        removeFromCart(id) {
            this.cart = this.cart.filter(i => i.id !== id);
        },
        get cartTotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
        },
        get cartCount() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        }
    }"
    @scroll.window="scrolled = (window.pageYOffset > 20) ? true : false">

    @if(session('success'))
    <div class="fixed top-24 right-6 z-70 bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3 animate-bounce">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <nav x-data="{ scrolled: false, mobileOpen: false }"
        :class="scrolled ? 'glass shadow-sm py-3' : 'bg-transparent py-5'"
        class="fixed w-full top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <a href="#" class="text-2xl font-extrabold text-slate-900 tracking-tight">SmartCaffee</a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-600">
                <a href="#hero" class="hover:text-orange-600 transition">Our Roast</a>
                <a href="#shop" class="hover:text-orange-600 transition">Menu</a>
                @auth
                <a href="#my-orders" class="hover:text-orange-600 transition">My Orders</a>
                <div class="flex items-center gap-3 bg-slate-100 px-4 py-2 rounded-full">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="text-slate-900">Hello, {{ explode(' ', Auth::user()->name)[0] }}</span>
                </div>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-slate-900 focus:outline-none">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-transition
            class="md:hidden bg-white shadow-lg rounded-b-2xl mt-2 px-6 py-4 space-y-4 text-sm font-semibold text-slate-700">
            <a href="#hero" class="block hover:text-orange-600 transition">Our Roast</a>
            <a href="#shop" class="block hover:text-orange-600 transition">Menu</a>
            @auth
            <a href="#my-orders" class="block hover:text-orange-600 transition">My Orders</a>
            <div class="flex items-center gap-3 bg-slate-100 px-4 py-2 rounded-full">
                <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                <span class="text-slate-900">Hello, {{ explode(' ', Auth::user()->name)[0] }}</span>
            </div>
            @endauth
        </div>
    </nav>


    <button x-show="cartCount > 0" @click="openModal = true"
        class="fixed bottom-8 right-8 z-40 bg-orange-600 text-white p-4 rounded-full shadow-2xl hover:bg-orange-700 transition-all flex items-center gap-3 group"
        x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-y-20 opacity-0" x-transition:enter-end="translate-y-0 opacity-100">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <span class="font-bold pr-2" x-text="cartCount + ' items • Ugx' + cartTotal"></span>
    </button>

    <section id="hero" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight mb-8">Wake Up to Smart <br><span class="text-orange-600 italic font-light">Coffee Solutions.</span></h1>
            <a href="#shop" class="px-8 py-4 bg-orange-600 text-white rounded-2xl font-bold text-lg hover:bg-orange-700 transition-all shadow-xl shadow-orange-200">Order Now</a>
        </div>
    </section>

    <section id="shop" class="py-24">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-extrabold mb-12">Today's Brews</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($products as $product)
                <div class="bg-white border border-slate-200 rounded-3xl p-6 hover:shadow-xl transition-all">
                    <h3 class="text-lg font-bold">{{ $product->name }}</h3>
                    <p class="text-2xl font-extrabold text-slate-900 mt-2 mb-6">Ugx {{ number_format($product->price, 2) }}</p>
                    <button @click="addToCart({id: {{ $product->id }}, name: '{{ $product->name }}', price: {{ $product->price }} })"
                        class="w-full py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-orange-600 transition-all">
                        Add to Cart
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @auth
    <section id="my-orders" class="py-24 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center gap-4 mb-12">
                <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight">Your Order History</h2>
            </div>

            @if($orders->isEmpty())
            <div class="text-center py-12 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-slate-400">You haven't placed any orders yet.</p>
            </div>
            @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($orders as $order)
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">#{{ $order->id }}</span>
                            <span class="text-slate-400 text-sm">{{ $order->created_at->format('M d, Y • H:i') }}</span>
                            <span class="ml-auto md:ml-0 px-3 py-1 rounded-full text-xs font-bold {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                    </div>
                    <div class="flex flex-col justify-center items-end border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-8">
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Total Amount</p>
                        <p class="text-2xl font-black text-slate-900">Ugx {{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endauth

    <div x-show="openModal" class="fixed inset-0 z-100 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openModal = false"></div>

            <div class="relative bg-white rounded-4xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">
                <div class="bg-orange-600 px-8 py-8 text-white">
                    <button @click="openModal = false" class="absolute top-6 right-6 text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h3 class="text-3xl font-extrabold">Complete Your Order</h3>
                </div>

                <form action="{{ route('orders.store') }}" method="POST" class="flex flex-col flex-1 p-8 overflow-y-auto">
                    @csrf
                    <input type="hidden" name="cart_data" :value="JSON.stringify(cart)">

                    <div class="mb-6 space-y-3 max-h-60 overflow-y-auto pr-2 custom-scroll">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div>
                                    <p class="font-bold text-slate-800" x-text="item.name"></p>
                                    <p class="text-xs text-slate-500" x-text="item.quantity + ' x Ugx' + item.price.toFixed(2)"></p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-orange-600" x-text="'Ugx' + (item.price * item.quantity).toFixed(2)"></span>
                                    <button type="button" @click="removeFromCart(item.id)" class="text-slate-300 hover:text-red-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="border-t border-slate-100 pt-4 mb-8 flex justify-between items-center">
                            <span class="text-slate-500 font-bold uppercase text-xs">Total Due</span>
                            <span class="text-3xl font-black text-slate-900" x-text="'Ugx' + cartTotal"></span>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <select name="salutation" required class="bg-slate-50 border-slate-200 rounded-xl py-3 focus:ring-orange-500">
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                </select>
                                <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" required class="col-span-2 bg-slate-50 border-slate-200 rounded-xl py-3 px-4" placeholder="Full Name">
                            </div>
                            <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" required class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4" placeholder="Email" {{ auth()->check() ? 'readonly' : '' }}>
                            <input type="tel" name="phone" required class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4" placeholder="Phone (+256...)">
                        </div>
                    </div>



                    <div class="mt-auto bg-white pt-4">
                        <button type="submit" class="w-full bg-orange-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-orange-700 transition-all shadow-lg shadow-orange-100">
                            Confirm Coffee Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <footer class="bg-slate-900 text-slate-400 py-12 text-center">
        <p class="text-sm">© 2026 SmartCaffee</p>
    </footer>

</body>

</html>