@props([
    'label' => 'Replace with approved photography',
    'tone' => 'produce',
])

<div
    {{ $attributes->class(['image-placeholder', "image-placeholder--{$tone}"]) }}
    role="img"
    aria-label="{{ $label }}"
>
    <span class="image-placeholder__tag">Photo pending</span>
</div>
