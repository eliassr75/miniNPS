<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        @auth
            <a href="#" class="headerButton" data-bs-toggle="modal" data-bs-target="#sidebarPanel">
                <ion-icon name="menu-outline"></ion-icon>
            </a>
        @endauth
    </div>
    <div class="pageTitle">
        <img src="{{ asset('assets/img/logo.png') }}" alt="logo" class="logo">
    </div>
    <div class="right">
        @auth
            <a href="app-notifications.html" class="headerButton">
                <ion-icon class="icon" name="notifications-outline"></ion-icon>
                <span class="badge badge-danger">4</span>
            </a>
            <a href="app-settings.html" class="headerButton">
                <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="image" class="imaged w32">
                <span class="badge badge-danger">6</span>
            </a>
        @endauth
    </div>
</div>
<!-- * App Header -->
