{{-- Brand colors from Admin > Brand Colors become CSS variables. Only valid 6-digit hex values are printed. --}}
@php
    $brand = setting_group('brand');
    $vars = [
        'primary' => 'primary', 'primary_dark' => 'primary-dark', 'secondary' => 'secondary', 'accent' => 'accent',
        'background' => 'background', 'surface' => 'surface', 'text' => 'text', 'text_muted' => 'text-muted',
        'border' => 'border', 'success' => 'success', 'danger' => 'danger',
    ];
@endphp
<style id="brand-vars">:root{@foreach($vars as $key => $css)@if(isset($brand[$key]) && preg_match('/^#[0-9a-fA-F]{6}$/', $brand[$key]))--brand-{{ $css }}:{{ $brand[$key] }};@endif @endforeach}</style>
