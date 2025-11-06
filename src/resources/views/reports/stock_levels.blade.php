<!doctype html>
<html>
<head><meta charset="utf-8"><title>Stock Levels</title></head>
<body>
<h1>Stock Levels</h1>
<p><a href="{{ request()->fullUrlWithQuery(['export'=>'csv']) }}">Download CSV</a></p>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>Branch</th><th>Product</th><th>On Hand</th><th>Reserved</th></tr></thead>
    <tbody>
    @forelse($rows as $r)
        <tr>
            <td>{{ $r->branch }}</td>
            <td>{{ $r->product }}</td>
            <td style="text-align:right">{{ (float)$r->on_hand }}</td>
            <td style="text-align:right">{{ (float)$r->reserved }}</td>
        </tr>
    @empty
        <tr><td colspan="4">No data</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
