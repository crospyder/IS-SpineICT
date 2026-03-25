<h1>Novi obligation</h1>

<form method="POST" action="/obligations">
@csrf

<select name="partner_id" required>
@foreach($partners as $p)
<option value="{{ $p->id }}">{{ $p->name }}</option>
@endforeach
</select>

<select name="partner_service_id">
<option value="">-- service --</option>
@foreach($services as $s)
<option value="{{ $s->id }}">{{ $s->name }}</option>
@endforeach
</select>

<input name="title" placeholder="Title" required>
<textarea name="description"></textarea>

<input name="status" value="open">
<input name="priority" value="normal">

<input type="date" name="due_date">

<button>Spremi</button>
</form>