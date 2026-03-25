<h1>Partner Services</h1>

<a href="/partner-services/create">Dodaj stavku</a>
<a href="/partner-services">Sve</a> |
<a href="/partner-services?expiring=1">Ističe uskoro</a>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Partner</th>
        <th>Tip</th>
        <th>Naziv</th>
        <th>Domena</th>
        <th>Provider</th>
        <th>Registrar</th>
        <th>Status</th>
        <th>Istek</th>
    </tr>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->partner?->name }}</td>
            <td>{{ $item->service_type }}</td>
            <td><a href="/partner-services/{{ $item->id }}">{{ $item->name }}</a></td>
            <td>{{ $item->domain_name }}</td>
            <td>{{ $item->provider }}</td>
            <td>{{ $item->registrar }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->expires_on }}</td>
        </tr>
    @endforeach
</table>