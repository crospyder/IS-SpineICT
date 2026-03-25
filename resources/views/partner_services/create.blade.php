<h1>Novi partner service</h1>

<form method="POST" action="/partner-services">
    @csrf

    <div>
        <label>Partner</label>
        <select name="partner_id" required>
            <option value="">-- odaberi --</option>
            @foreach($partners as $partner)
                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Tip usluge</label>
        <input type="text" name="service_type" placeholder="domain / hosting / licence" required>
    </div>

    <div>
        <label>Naziv</label>
        <input type="text" name="name" required>
    </div>

    <div>
        <label>Domena</label>
        <input type="text" name="domain_name">
    </div>

    <div>
        <label>Provider</label>
        <input type="text" name="provider">
    </div>

    <div>
        <label>Registrar</label>
        <input type="text" name="registrar">
    </div>

    <div>
        <label>Status</label>
        <input type="text" name="status" value="active" required>
    </div>

    <div>
        <label>Periodika</label>
        <input type="text" name="renewal_period" placeholder="yearly">
    </div>

    <div>
        <label>Početak</label>
        <input type="date" name="starts_on">
    </div>

    <div>
        <label>Istek</label>
        <input type="date" name="expires_on">
    </div>

    <div>
        <label>Renewal date</label>
        <input type="date" name="renewal_date">
    </div>

    <div>
        <label>Admin link</label>
        <input type="text" name="admin_link">
    </div>

    <div>
        <label>Način produljenja</label>
        <input type="text" name="renewal_method">
    </div>

    <div>
        <label>Auto renew</label>
        <input type="checkbox" name="auto_renew" value="1">
    </div>

    <div>
        <label>Aktivno</label>
        <input type="checkbox" name="is_active" value="1" checked>
    </div>

    <div>
        <label>Bilješke</label>
        <textarea name="notes"></textarea>
    </div>

    <button type="submit">Spremi</button>
</form>

<a href="/partner-services">Nazad</a>