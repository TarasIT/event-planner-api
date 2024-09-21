@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="https://res.cloudinary.com/dy1bavapq/image/upload/v1726946781/event-planner-icon_tl7kmn.png"
                    class="logo" alt="Event-planner Logo">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
