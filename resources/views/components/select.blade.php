<div class="select" style="width:200px;">
    <select @if ($class) class="{{ $class }}" @endif>
        @if ($placeholder)
        <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $option)
        <option
            value="{{ $option['value'] }}"
            @if(!empty($selected) && $option['value'] === $selected)
            selected
            @endif
        >
            {{ $option['name'] }}
        </option>
        @endforeach
    </select>
</div>
