<!doctype html>
<html lang="ka">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#141B3C">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'კიდზიო' }}</title>
    <meta name="description" content="მხიარული საგანმანათლებლო თამაშები 7–12 წლის ბავშვებისთვის.">

    <link rel="stylesheet" href="/assets/fonts.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Picks the world from ?theme=…, then localStorage "kidzio-theme", else space; loaded synchronously so KIDZIO_THEME exists before app.js runs. --}}
    <script>
        (function () {
            var themes = ['space', 'autumn', 'cars', 'ocean'];
            var pick = new URLSearchParams(location.search).get('theme');
            if (!pick) { try { pick = localStorage.getItem('kidzio-theme'); } catch (e) {} }
            if (themes.indexOf(pick) < 0) pick = 'space';
            document.documentElement.setAttribute('data-theme', pick);
            document.write('<link rel="stylesheet" href="/themes/' + pick + '/theme.css">');
            document.write('<script src="/themes/' + pick + '/theme.js"><\/script>');
        })();
    </script>
</head>

<body class="font-body">
    @include('partials.sprite')
    <svg id="theme-sprite" width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"></svg>

    {{ $slot }}
</body>
</html>
