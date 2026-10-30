@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'rows' => null, 'required' => false, 'options' => null, 'placeholder' => null])
<div {{ $attributes->only('class')->merge(['class' => 'field']) }}>
  <label for="f-{{ $name }}">{{ $label }}@if ($required)<i>*</i>@endif</label>
  @if ($rows)
    <textarea id="f-{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif @required($required) {{ $attributes->except('class') }}>{{ old($name, $value) }}</textarea>
  @elseif ($options !== null)
    <select id="f-{{ $name }}" name="{{ $name }}" @required($required) {{ $attributes->except('class') }}>
      @foreach ($options as $key => $text)
        <option value="{{ $key }}" @selected((string) old($name, $value) === (string) $key)>{{ $text }}</option>
      @endforeach
    </select>
  @else
    <input id="f-{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif @required($required) {{ $attributes->except('class') }}>
  @endif
  @if ($hint)<small>{{ $hint }}</small>@endif
  @error($name)<em class="err">{{ $message }}</em>@enderror
</div>
