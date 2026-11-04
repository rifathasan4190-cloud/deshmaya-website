<h1>New {{ ucfirst($type) }}</h1>
<p>A visitor submitted a {{ $type }} through the Deshmaya website.</p>
<dl>
    @foreach ($submission as $label => $value)
        @if (filled($value))
            <dt><strong>{{ str($label)->replace('_', ' ')->title() }}</strong></dt>
            <dd style="{{ $label === 'message' ? 'white-space: pre-line' : '' }}">{{ $value }}</dd>
        @endif
    @endforeach
</dl>
