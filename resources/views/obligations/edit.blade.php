<h1>Edit</h1>

<form method="POST" action="/obligations/{{ $item->id }}">
@csrf
@method('PUT')

<select name="partner_id">
@foreach($partners as $p)
<option value="{{ $p->id }}" {{ $item->partner_id == $p->id ? 'selected' : '' }}>
{{ $p->name }}
</option>
@endforeach
</select>

<select name="partner_service_id">
<option value="">--</option>
@foreach($services as $s)
<option value="{{ $s->id }}" {{ $item->partner_service_id == $s->id ? 'selected' : '' }}>
{{ $s->name }}
</option>
@endforeach
</select>

<input name="title" value="{{ $item->title }}">
<textarea name="description">{{ $item->description }}</textarea>

<input name="status" value="{{ $item->status }}">
<input name="priority" value="{{ $item->priority }}">

<input type="date" name="due_date" value="{{ optional($item->due_date)->format('Y-m-d') }}">

<button>Spremi</button>
</form>