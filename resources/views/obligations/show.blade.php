<h1>{{ $item->title }}</h1>

<p>Partner: {{ $item->partner?->name }}</p>
<p>Service: {{ $item->partnerService?->name }}</p>
<p>Status: {{ $item->status }}</p>
<p>Priority: {{ $item->priority }}</p>
<p>Due: {{ $item->due_date }}</p>

<a href="/obligations/{{ $item->id }}/edit">Edit</a>

<form method="POST" action="/obligations/{{ $item->id }}">
@csrf
@method('DELETE')
<button>Delete</button>
</form>