<h1>{{ $item->name }}</h1>

<p><strong>Partner:</strong> {{ $item->partner?->name }}</p>
<p><strong>Tip:</strong> {{ $item->service_type }}</p>
<p><strong>Domena:</strong> {{ $item->domain_name }}</p>
<p><strong>Provider:</strong> {{ $item->provider }}</p>
<p><strong>Registrar:</strong> {{ $item->registrar }}</p>
<p><strong>Status:</strong> {{ $item->status }}</p>
<p><strong>Periodika:</strong> {{ $item->renewal_period }}</p>
<p><strong>Početak:</strong> {{ $item->starts_on }}</p>
<p><strong>Istek:</strong> {{ $item->expires_on }}</p>
<p><strong>Renewal:</strong> {{ $item->renewal_date }}</p>
<p><strong>Admin link:</strong> {{ $item->admin_link }}</p>
<p><strong>Način produljenja:</strong> {{ $item->renewal_method }}</p>
<p><strong>Auto renew:</strong> {{ $item->auto_renew ? 'Da' : 'Ne' }}</p>
<p><strong>Aktivno:</strong> {{ $item->is_active ? 'Da' : 'Ne' }}</p>
<p><strong>Bilješke:</strong> {{ $item->notes }}</p>

<a href="/partner-services">Nazad</a>
<a href="/partner-services/{{ $item->id }}/edit">Edit</a>
<a href="/obligations/create?service_id={{ $item->id }}">
    ➕ Dodaj obligation
</a>

<form method="POST" action="/partner-services/{{ $item->id }}" style="margin-top:20px;">
    @csrf
    @method('DELETE')
    <button type="submit">Delete</button>
</form>