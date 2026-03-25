<h1>Partners</h1>

<a href="/partners/create">Dodaj partnera</a>

<ul>
@foreach($partners as $p)
    <li>{{ $p->name }}</li>
@endforeach
</ul>