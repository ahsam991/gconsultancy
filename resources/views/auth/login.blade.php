<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Global Consultancy Education</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-white dark:bg-gray-900 min-h-screen font-sans">

    <div class="max-w-md mx-auto p-8 mt-12">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Sign In</h2>
            
            @if (session('status'))
                <div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/auth/login">
                @csrf()
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="enter@email.com"
                        required
                    >
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <div class="mb-8">
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 text-white font-medium py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Sign In
                    </button>
                </div>

                <div class="text-center text-sm text-gray-600">
                    <a href="/auth/forgot-password" class="underline hover:text-blue-600">Forgot password?</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>