<h1>Partners</h1>

<a href="/partners/create">Dodaj partnera</a>

<ul>
    @foreach($partners as $p)
        <li>
            <a href="/partners/{{ $p->id }}">
                {{ $p->name }}
            </a>
        </li>
    @endforeach
</ul>