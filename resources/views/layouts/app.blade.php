<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeland Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="mobile-topbar">
        <button id="btnMobileMenu" class="hamburger-btn">
            ☰
        </button>
        
        <div class="brand-mobile">
            <span>H</span> Homeland
        </div>
    </div>

    <div id="mobileOverlay" class="mobile-overlay"></div>

    <div class="sidebar">
        <div class="brand">
            <span>H</span> Homeland <br>
        </div>
        

        <button id="btnGlobalAgregar" class="btn-add-main">
            + Agregar producto
        </button>

        <ul class="nav-menu">
            <li class="nav-item active" data-target="dashboard">
                <span class="ant-design--dashboard-twotone"></span> Dashboard
            </li>
            <li class="nav-item" data-target="productos">
                <span class="solar--cart-large-4-bold-duotone"></span> Productos
            </li>
        </ul>
    </div>

    <div class="main-content">
        @yield('content')
    </div>
<div class="fab-add" onclick="$('#btnGlobalAgregar').click()">+</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
</body>
</html>