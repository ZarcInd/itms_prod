@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<!-- <img src="{{ asset('public/frontend/assets/images/logo/logo.png')}}" class="logo" alt="AArvy Logo"> -->
 <h1>PRIME EDGE</h1>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
