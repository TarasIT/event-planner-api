@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="{{ asset('event-planner-icon.png') }}" class="logo" alt="Event-planner Logo">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
