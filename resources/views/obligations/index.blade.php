<h1>Obligations</h1>

<a href="/obligations">Sve</a> |
<a href="/obligations?status=open">Otvoreno</a> |
<a href="/obligations?due=soon">Uskoro</a> |
<a href="/obligations?due=overdue">Kasni</a>

<br><br>

<a href="/obligations/create">Dodaj</a>

<table border="1" cellpadding="8">
<tr>
    <th>ID</th>
    <th>Partner</th>
    <th>Service</th>
    <th>Title</th>
    <th>Status</th>
    <th>Priority</th>
    <th>Due</th>
</tr>

@foreach($items as $i)
<tr>
    <td>{{ $i->id }}</td>
    <td>{{ $i->partner?->name }}</td>
    <td>{{ $i->partnerService?->name }}</td>
    <td><a href="/obligations/{{ $i->id }}">{{ $i->title }}</a></td>
    <td>{{ $i->status }}</td>
    <td>{{ $i->priority }}</td>

    @php
        $days = $i->due_date ? now()->diffInDays($i->due_date, false) : null;
    @endphp

    <td style="
        @if($days !== null && $days < 0) background:red;
        @elseif($days !== null && $days <= 3) background:orange;
        @elseif($days !== null && $days <= 7) background:yellow;
        @endif
    ">
        {{ $i->due_date }}
    </td>
</tr>
@endforeach
</table>