<ul>
    <li>Author: <strong>{{ $pasta->author }}</strong></li>
    <li>Created: <strong>{{ $pasta->created_at }}</strong></li>
    {{-- @todo Set link to parent--}}
    <li>Parent: <a href="{{ route('pasta.show', $pasta->uuid) }}"><strong>{{ $pasta->uuid }}</strong></a></li>
    <li>Here!</li>
</ul>
