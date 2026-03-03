<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | {{ config('app.name', 'ITSSMO-Tools') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .animate-bounce-slow {
            animation: bounce 3s infinite;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-900 to-blue-950 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-2xl w-full text-center animate-fade-in">
        <!-- Emoji Icon -->
        <div class="text-7xl md:text-8xl mb-6 animate-bounce-slow">🔍</div>

        <!-- Status Badge -->
        <span class="inline-block bg-blue-500/10 border border-blue-500/30 text-blue-400 px-4 py-2 rounded-full text-sm font-semibold uppercase tracking-wider mb-4">
            404 Error
        </span>

        <!-- Error Code -->
        <h1 class="text-8xl md:text-9xl font-black bg-gradient-to-r from-blue-500 to-cyan-400 bg-clip-text text-transparent leading-none mb-4">
            404
        </h1>

        <!-- Title -->
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Page Not Found
        </h2>

        <!-- Description -->
        <p class="text-lg text-gray-400 mb-8 max-w-lg mx-auto">
            The page you're looking for seems to have wandered off into the digital void. Let's get you back on track.
        </p>

        <!-- Details Card -->
        <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-6 mb-8 text-left">
            <div class="flex gap-4 mb-4">
                <span class="material-symbols-sharp text-3xl text-blue-400">location_on</span>
                <div>
                    <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">What happened</h4>
                    <p class="text-white">The requested resource could not be found on this server.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <span class="material-symbols-sharp text-3xl text-amber-400">lightbulb</span>
                <div>
                    <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">What you can do</h4>
                    <p class="text-white">Try going back, checking the URL, or navigating to the homepage.</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/25">
                <span class="material-symbols-sharp mr-2">home</span>
                Return Home
            </a>
            <button onclick="window.history.back()" class="inline-flex items-center justify-center px-6 py-3 bg-transparent border border-blue-500 text-blue-400 hover:bg-blue-500/10 font-semibold rounded-lg transition-all duration-300 hover:-translate-y-1">
                <span class="material-symbols-sharp mr-2">arrow_back</span>
                Go Back
            </button>
        </div>

        <!-- Footer -->
        <p class="text-sm text-gray-500 mt-8">
            Having trouble? <a href="mailto:itss@gordoncollege.edu.ph" class="text-blue-400 hover:underline">Contact our support team</a>
        </p>
    </div>
</body>
</html>
