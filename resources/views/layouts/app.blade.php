<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    @include('partials.schema')
</head>
<body>
    <a class="skip-link" href="#content">Skip to content</a>
    @include('partials.loader')
    @include('partials.header')
    <main id="content">
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
