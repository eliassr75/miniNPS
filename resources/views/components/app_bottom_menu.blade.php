    <!-- App Bottom Menu -->
<div class="appBottomMenu">
    <a href="{{ route('dashboard') }}" class="item {{ request()->routeIs('dashboard') ? 'active': "" }}">
        <div class="col">
            <ion-icon name="pie-chart-outline"></ion-icon>
            <strong>Dashboard</strong>
        </div>
    </a>
    <a href="{{ route('users.index') }}" class="item {{ request()->routeIs('users.index') ? 'active': "" }}">
        <div class="col">
            <ion-icon name="people-outline"></ion-icon>
            <strong>Clientes</strong>
        </div>
    </a>
    <a href="{{ route('nps.all') }}" class="item {{ request()->routeIs('nps.all') ? 'active': "" }}">
        <div class="col">
            <ion-icon name="create-outline"></ion-icon>
            <strong>Pesquisas</strong>
        </div>
    </a>
    <a href="app-settings.html" class="item">
        <div class="col">
            <ion-icon name="settings-outline"></ion-icon>
            <strong>Configurações</strong>
        </div>
    </a>
</div>
<!-- * App Bottom Menu -->
