<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
</head>
<body>
    Bonjour
    <a href="{{route('login')}} ">Se connecter</a>
    <form method="POST" action="{{ route('logout') }}">
  @csrf
  <button type="submit">Me déconnecter</button>
</form>
</body>
</html>