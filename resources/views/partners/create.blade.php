<h1>Novi partner</h1>

<form method="POST" action="/partners">
    @csrf
    <input type="text" name="name" placeholder="Naziv firme" required>
    <button type="submit">Spremi</button>
</form>

<a href="/partners">Nazad</a>