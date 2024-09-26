
<p>Text: {{ isset($demo_text) ? $demo_text : '' }}</p>

<p>Number: {{ isset($demo_number) ? $demo_number : '' }}</p>

<p>Textarea: {{ isset($demo_textarea) ? $demo_textarea : '' }}</p>

<p>Checkbox: {{ isset($demo_checkbox) ? var_dump($demo_checkbox) : '' }}</p>

<p>Rich Text: {!! isset($demo_richtext) ? $demo_richtext : '' !!}</p>

<p>Color: {{ isset($demo_color) ? $demo_color : '' }}</p>

<p>Select: {{ isset($demo_select) ? $demo_select : '' }}</p>

<p>Image: {{ isset($demo_image) ? $demo_image : '' }}</p>

<p>Upload file multiple: {!! isset($demo_file) ? $demo_file : '' !!}</p>

<p>Upload repeatable: 
    @foreach (isset($demo_repeatable) ? $demo_repeatable : [] as $index =>  $item)
        <p>{{ $index }}. Repeatable Text: {{ isset($item['demo_repeat_text']) ? $item['demo_repeat_text'] : '' }}</p>
        <p>{{ $index }}. Repeatable File: {!! isset($item['demo_repeat_file']) ? $item['demo_repeat_file'] : '' !!}</p>
    @endforeach
</p>

