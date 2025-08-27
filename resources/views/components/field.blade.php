@props(['label' => null, 'class' => ''])
<div {{ $attributes->merge(['class' => $class]) }}>
    @if ($label)
        <label class="block text-sm mb-1">{{ $label }}</label>
    @endif
    {{ $slot }}
    @error($attributes->get('name'))
        <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
    @enderror
</div>
