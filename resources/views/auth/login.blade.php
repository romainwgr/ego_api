{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
  <h2 class="text-2xl font-bold mb-6">Connexion</h2>

  {{-- Affichage des messages d’erreur --}}
  @if($errors->any())
    <div class="mb-4 text-red-600">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  
  <form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Identifiant --}}
    <div class="mb-4">
      <label for="email" class="block font-medium">Email ou Pseudo</label>
      <input
        id="email"
        name="email"
        type="email"
        value="{{ old('login') }}"
        required
        autofocus
        class="mt-1 w-full border-gray-300 rounded"
      >
    </div>

    {{-- Mot de passe --}}
    <div class="mb-4">
      <label for="password" class="block font-medium">Mot de passe</label>
      <input
        id="password"
        name="password"
        type="password"
        required
        class="mt-1 w-full border-gray-300 rounded"
      >
    </div>

    {{-- Soumettre ou réinitialiser --}}
    <div class="flex items-center justify-between mb-4">
      <button
        type="submit"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Se connecter
      </button>
      <a
        href="{{ route('password.request') }}"
        class="text-sm text-blue-600 hover:underline"
      >
        Mot de passe oublié ?
      </a>
    </div>

    {{-- Option SSO Google --}}
    <div class="text-center">
      <span class="text-gray-500">ou</span>
      <div class="mt-3">
        <a
          href="{{ route('google.login') }}"
          class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Se connecter avec Google
        </a>
      </div>
    </div>
  </form>
</div>
@endsection
