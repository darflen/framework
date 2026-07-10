@for($index = 0; $index <= 3; $index++)
    @if($index % $bar)
    Foo
    @else
    Bar
    @endif
@endfor
