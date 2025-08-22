{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Mon App')</title>
  {{-- Si tu utilises Tailwind/Vite : --}}
  {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 text-gray-800">

  <nav class="bg-white shadow p-4">
    <div class="container mx-auto">
      <a href="{{ url('/') }}" class="font-bold">Accueil</a>
      @auth
        <span class="ml-4">Bonjour, {{ auth()->user()->username }}</span>
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="ml-4 text-red-600">Déconnexion</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="ml-4">Connexion</a>
      @endauth
    </div>
  </nav>

  <main class="container mx-auto py-8">
    @yield('content')
  </main>

</body>
</html>
