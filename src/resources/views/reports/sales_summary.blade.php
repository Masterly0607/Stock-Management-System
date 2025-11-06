<!doctype html>
<html>
<head><meta charset="utf-8"><title>Sales Summary</title></head>
<body>
<h1>Sales Summary</h1>
<form method="get">
    From <input type="date" name="date_from" value="{{ request('date_from', $from->toDateString()) }}">
    To <input type="date" name="date_to" value="{{ request('date_to', $to->toDateString()) }}">
    <button type="submit">Filter</button>
    <a href="{{ request()->fullUrlWithQuery(['export'=>'csv']) }}">Download CSV</a>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>Branch</th><th>Product</th><th>Qty Sold</th><th>Gross Amount</th></tr></thead>
    <tbody>
    @forelse($rows as $r)
        <tr>
            <td>{{ $r->branch }}</td>
            <td>{{ $r->product }}</td>
            <td style="text-align:right">{{ (float)$r->qty_sold }}</td>
            <td style="text-align:right">{{ number_format((float)$r->gross_amount, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="4">No data</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
