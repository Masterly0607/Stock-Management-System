<!doctype html>
<html>
<head><meta charset="utf-8"><title>Low Stock</title></head>
<body>
<h1>Low Stock</h1>
<form method="get">
    Threshold <input type="number" name="threshold" value="{{ request('threshold', $threshold) }}">
    <button type="submit">Apply</button>
    <a href="{{ request()->fullUrlWithQuery(['export'=>'csv']) }}">Download CSV</a>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>Branch</th><th>Product</th><th>On Hand</th></tr></thead>
    <tbody>
    @forelse($rows as $r)
        <tr>
            <td>{{ $r->branch }}</td>
            <td>{{ $r->product }}</td>
            <td style="text-align:right">{{ (float)$r->on_hand }}</td>
        </tr>
    @empty
        <tr><td colspan="3">No low-stock items 🎉</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
