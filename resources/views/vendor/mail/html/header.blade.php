@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ config('app.url_logo') }}" class="logo" alt="Logo {{ config('app.name') }}">
@endif
</a>
</td>
</tr>
