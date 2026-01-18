<li>
    @if($node->children->isNotEmpty())
        <span class="caret">{{ $node->name }} --- {{ $node->code }}</span>
        <ul class="nested">
            @foreach($node->children as $child)
                @include('admin.accounts.partials.tree-node', ['node' => $child])
            @endforeach
        </ul>
    @else
        {{ $node->name }} --- {{ $node->code }}
    @endif
</li>
