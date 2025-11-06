<!doctype html>
<html>
<head><meta charset="utf-8"><title>Audit Logs</title></head>
<body>
<h1>Audit Logs</h1>
<form method="get">
    From <input type="date" name="date_from" value="{{ request('date_from', $from->toDateString()) }}">
    To <input type="date" name="date_to" value="{{ request('date_to', $to->toDateString()) }}">
    Action <input type="text" name="action" value="{{ request('action') }}" placeholder="e.g. sales.delivered">
    <button type="submit">Filter</button>
    <a href="{{ request()->fullUrlWithQuery(['export'=>'csv']) }}">Download CSV</a>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>Time</th><th>Action</th><th>Entity</th><th>User</th><th>Payload</th>
        </tr>
    </thead>
    <tbody>
    @forelse($rows as $r)
        <tr>
            <td>{{ $r->created_at }}</td>
            <td>{{ $r->action }}</td>
            <td>{{ $r->entity_type }} #{{ $r->entity_id }}</td>
            <td>{{ $r->user_id ?? '-' }}</td>
            <td><pre style="white-space:pre-wrap;max-width:600px">{{ $r->payload }}</pre></td>
        </tr>
    @empty
        <tr><td colspan="5">No logs</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
