<!doctype html>
<html>
<head><meta charset="utf-8"><title>Transfer Summary</title></head>
<body>
<h1>Transfer Summary</h1>
<form method="get">
    From <input type="date" name="date_from" value="{{ request('date_from', $from->toDateString()) }}">
    To <input type="date" name="date_to" value="{{ request('date_to', $to->toDateString()) }}">
    <button type="submit">Filter</button>
    <a href="{{ request()->fullUrlWithQuery(['export'=>'csv']) }}">Download CSV</a>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>Branch</th><th>Product</th><th>Qty In</th><th>Qty Out</th><th>Net Qty</th></tr></thead>
    <tbody>
    @forelse($rows as $r)
        <tr>
            <td>{{ $r->branch }}</td>
            <td>{{ $r->product }}</td>
            <td style="text-align:right">{{ (float)$r->qty_in }}</td>
            <td style="text-align:right">{{ (float)$r->qty_out }}</td>
            <td style="text-align:right">{{ (float)$r->net_qty }}</td>
        </tr>
    @empty
        <tr><td colspan="5">No data</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
