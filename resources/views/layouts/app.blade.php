<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name','Abrafin') }}</title>
    @vite(['resources/sass/app.scss','resources/js/app.js'])
</head>
<body style="background: rgb(100 190 240) url('/images/abrafinbg.jpg') no-repeat top left; background-size: cover;min-height: 100vh;">
    <div id="app">
        <nav class="navbar navbar-expand-lg bg-body-tertiary bg-dark"  data-bs-theme="dark">
            <div class="container-fluid">
                <!--<a class="navbar-brand" href="#">Navbar</a>-->
                @auth
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <x-nav-link href="/dashboard" :active="request()->is('/dashboard')">Дашборд</x-nav-link>
                        <x-nav-link href="/deals" :active="request()->is('/deals')">Договоры</x-nav-link>
                        <x-nav-link href="/clients" :active="request()->is('clients')">Клиенты</x-nav-link>
                        <x-nav-link href="/repayments" :active="request()->is('repayments')">Поступления</x-nav-link>
                        <x-nav-link href="/reports" :active="request()->is('reports')">Отчеты</x-nav-link>
                        <x-nav-link href="#">&nbsp;</x-nav-link>
                        <x-nav-link href="#" :active="false">Hello {{ Auth::user()->name }}</x-nav-link>
                    </ul>
                    
                    <form id="logoutform" method="POST" action="/logout">
                        @csrf
                        <button form="logoutform" type="submit" class="btn btn-primary">Выход</button>
                    </form>        
                    
                </div>
                @endauth
                
            </div>
        </nav>
        @yield('content')
    </div>
    
</body>
</html>