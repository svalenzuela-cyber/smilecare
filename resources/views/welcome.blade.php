<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SmileCare') }} - Dental Clinic</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800 bg-white">

    <!-- Header -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <x-application-logo class="h-9 w-auto fill-current text-teal-600" />
                <span class="font-bold text-lg text-teal-800">SmileCare</span>
            </a>

            <nav class="flex items-center gap-3">
                <a href="#services" class="hidden sm:inline text-sm text-gray-600 hover:text-teal-700">Services</a>
                <a href="#dentists" class="hidden sm:inline text-sm text-gray-600 hover:text-teal-700 me-2">Dentists</a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 bg-teal-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-teal-700 hover:underline">Log in</a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 bg-teal-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700">
                        Book appointment
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="dental-pattern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <p class="text-sm font-semibold text-teal-700 uppercase tracking-widest">Your smile, our priority</p>
                <h1 class="mt-3 text-4xl sm:text-5xl font-bold text-teal-900 leading-tight">
                    Book your dental visit online in minutes
                </h1>
                <p class="mt-5 text-lg text-gray-600">
                    Choose your service, pick the dentist you are comfortable with, and select a time that fits your schedule.
                    No more waiting in line or calling the clinic.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="px-6 py-3 bg-teal-600 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-teal-700">
                            Go to dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="px-6 py-3 bg-teal-600 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-teal-700">
                            Book an appointment
                        </a>
                        <a href="{{ route('login') }}"
                           class="px-6 py-3 bg-white border border-teal-600 rounded-md font-semibold text-sm text-teal-700 uppercase tracking-widest hover:bg-teal-50">
                            I have an account
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Highlight card -->
            <div class="bg-white rounded-3xl shadow-lg p-8 border-t-8 border-teal-500">
                <x-application-logo class="w-16 h-16 fill-current text-teal-500" />
                <p class="mt-4 text-2xl font-bold text-teal-900">Why patients choose SmileCare</p>
                <ul class="mt-4 space-y-3 text-gray-700">
                    <li class="flex gap-3"><span class="text-teal-600 font-bold">✓</span> Book 24/7 from your phone or computer</li>
                    <li class="flex gap-3"><span class="text-teal-600 font-bold">✓</span> Choose your preferred dentist</li>
                    <li class="flex gap-3"><span class="text-teal-600 font-bold">✓</span> See prices before you book</li>
                    <li class="flex gap-3"><span class="text-teal-600 font-bold">✓</span> Track and cancel your appointments online</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Services (from the database) -->
    <section id="services" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-teal-900 text-center">Our services</h2>
            <p class="mt-2 text-center text-gray-600">Clear prices and estimated time for every treatment.</p>

            <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($services as $service)
                    <div class="rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
                        <p class="text-lg font-semibold text-gray-800">{{ $service->name }}</p>
                        @if ($service->description)
                            <p class="mt-2 text-sm text-gray-600">{{ $service->description }}</p>
                        @endif
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-bold text-teal-700">₱{{ number_format($service->price, 2) }}</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-teal-50 text-teal-800">{{ $service->duration_minutes }} min</span>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">Services will be listed here soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Dentists (from the database) -->
    <section id="dentists" class="py-16 bg-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-teal-900 text-center">Meet our dentists</h2>
            <p class="mt-2 text-center text-gray-600">Pick the dentist you are most comfortable with when you book.</p>

            <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($dentists as $dentist)
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <div class="w-14 h-14 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xl font-bold">
                            {{ strtoupper(substr($dentist->name, 0, 1)) }}
                        </div>
                        <p class="mt-4 text-lg font-semibold text-gray-800">{{ $dentist->name }}</p>
                        <p class="text-sm text-teal-700">{{ $dentist->specialization }}</p>
                        @if ($dentist->bio)
                            <p class="mt-2 text-sm text-gray-600">{{ $dentist->bio }}</p>
                        @endif
                        <p class="mt-4 text-xs text-gray-500">{{ $dentist->workingDaysLabel() }} · {{ $dentist->workingHoursLabel() }}</p>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">Our dentists will be listed here soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-teal-900 text-center">How it works</h2>
            <div class="mt-10 grid sm:grid-cols-3 gap-6 text-center">
                <div>
                    <div class="mx-auto w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold">1</div>
                    <p class="mt-4 font-semibold">Create an account</p>
                    <p class="text-sm text-gray-600">Sign up with your name and email.</p>
                </div>
                <div>
                    <div class="mx-auto w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold">2</div>
                    <p class="mt-4 font-semibold">Book a schedule</p>
                    <p class="text-sm text-gray-600">Choose a service, dentist, date, and time.</p>
                </div>
                <div>
                    <div class="mx-auto w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold">3</div>
                    <p class="mt-4 font-semibold">Get confirmed</p>
                    <p class="text-sm text-gray-600">The clinic confirms your visit. See you there!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-teal-900 text-teal-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
            <div class="flex items-center gap-2">
                <x-application-logo class="h-6 w-auto fill-current text-teal-300" />
                <span class="font-semibold text-white">SmileCare</span>
            </div>
            <p>&copy; {{ date('Y') }} SmileCare Dental Appointment</p>
        </div>
    </footer>

</body>
</html>