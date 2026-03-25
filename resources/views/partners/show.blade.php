<h1>{{ $partner->name }}</h1>

<a href="/partners">Nazad</a>
<a href="/partners/{{ $partner->id }}/edit">Edit</a>

<form method="POST" action="/partners/{{ $partner->id }}" style="margin-top: 20px;">
    @csrf
    @method('DELETE')
    <button type="submit">Delete</button>
</form>