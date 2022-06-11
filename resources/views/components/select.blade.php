<div class="select-wrapper">
    <select @if ($class) class="select {{ $class }}" @endif>
        @if ($placeholder)
        <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $option)
        <option
            value="{{ $option['value'] }}"
            {{ (!empty($selected) && $option['value']) === $selected ? 'selected' : '' }}
        >{{ $option['name'] }}</option>
        @endforeach
    </select>
</div>
