{{-- resources/views/auth/choose-account.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
  <h2 class="text-xl font-bold mb-4">Plusieurs comptes trouvés</h2>
  <p class="mb-4">Votre identifiant est associé à plusieurs comptes. Merci de choisir celui sur lequel vous souhaitez vous connecter :</p>

  <form method="POST" action="{{ route('login.choose') }}">
    @csrf

    {{-- Champ caché pour réutiliser l’identifiant --}}
    <input type="hidden" name="login" value="{{ old('login', $login) }}">

    {{-- Champ caché pour réutiliser le mot de passe --}}
    <input type="hidden" name="password" value="{{ $password }}">

    {{-- Liste des comptes valides --}}
    @foreach($users as $user)
      <label class="flex items-center mb-3">
        <input type="radio" name="user_id" value="{{ $user->id }}"
               class="form-radio h-4 w-4 text-blue-600"
               required>
        <span class="ml-2">{{ $user->username }} (inscrit le {{ $user->created_at->format('Y-m-d') }})</span>
      </label>
    @endforeach

    {{-- Bouton de soumission --}}
    <div class="mt-6">
      <button type="submit"
              class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">
        Se connecter
      </button>
    </div>
  </form>
</div>
@endsection
