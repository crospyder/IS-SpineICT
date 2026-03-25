<h1>Edit partner service</h1>

<form method="POST" action="/partner-services/{{ $item->id }}">
    @csrf
    @method('PUT')

    <div>
        <label>Partner</label>
        <select name="partner_id" required>
            @foreach($partners as $partner)
                <option value="{{ $partner->id }}" {{ $item->partner_id == $partner->id ? 'selected' : '' }}>
                    {{ $partner->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Tip usluge</label>
        <input type="text" name="service_type" value="{{ $item->service_type }}" required>
    </div>

    <div>
        <label>Naziv</label>
        <input type="text" name="name" value="{{ $item->name }}" required>
    </div>

    <div>
        <label>Domena</label>
        <input type="text" name="domain_name" value="{{ $item->domain_name }}">
    </div>

    <div>
        <label>Provider</label>
        <input type="text" name="provider" value="{{ $item->provider }}">
    </div>

    <div>
        <label>Registrar</label>
        <input type="text" name="registrar" value="{{ $item->registrar }}">
    </div>

    <div>
        <label>Status</label>
        <input type="text" name="status" value="{{ $item->status }}" required>
    </div>

    <div>
        <label>Periodika</label>
        <input type="text" name="renewal_period" value="{{ $item->renewal_period }}">
    </div>

    <div>
        <label>Početak</label>
        <input type="date" name="starts_on" value="{{ optional($item->starts_on)->format('Y-m-d') }}">
    </div>

    <div>
        <label>Istek</label>
        <input type="date" name="expires_on" value="{{ optional($item->expires_on)->format('Y-m-d') }}">
    </div>

    <div>
        <label>Renewal date</label>
        <input type="date" name="renewal_date" value="{{ optional($item->renewal_date)->format('Y-m-d') }}">
    </div>

    <div>
        <label>Admin link</label>
        <input type="text" name="admin_link" value="{{ $item->admin_link }}">
    </div>

    <div>
        <label>Način produljenja</label>
        <input type="text" name="renewal_method" value="{{ $item->renewal_method }}">
    </div>

    <div>
        <label>Auto renew</label>
        <input type="checkbox" name="auto_renew" value="1" {{ $item->auto_renew ? 'checked' : '' }}>
    </div>

    <div>
        <label>Aktivno</label>
        <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
    </div>

    <div>
        <label>Bilješke</label>
        <textarea name="notes">{{ $item->notes }}</textarea>
    </div>

    <button type="submit">Spremi</button>
</form>

<a href="/partner-services/{{ $item->id }}">Nazad</a>