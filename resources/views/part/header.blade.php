{{-- Верхнее меню пользователя --}}

<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>
    <div class="navbar-menu">
        <div class="navbar-start">


            <a class="navbar-item">

                <div class="navbar-item-username-content">
                    <span class="material-icons">
                        menu
                    </span>
                </div>
            </a>
                    
            <a class="navbar-item">

                <div class="navbar-item-username-content">
                    <span class="material-icons">
                    bookmark_border
                    </span>
                </div>
            </a>
            <a class="navbar-item">

                <div class="navbar-item-username-content">
                    <span class="material-icons">
                        settings
                    </span>
                </div>
            </a>
        </div>


        <div class="navbar-center">

            <a class="navbar-item">
                <div class="control has-icons-right">
                    <input class="input"  placeholder="Поиск" />
                    <span class="icon is-right">
                        <span class="material-icons">
                            search
                        </span>
                    </span>
                </div>
            </a>
        </div>


        <div class="navbar-end">

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    <div class="navbar-item-username">
                        <div class="navbar-item-username-content">
                            <span class="material-icons">
                                account_circle
                            </span>
                        </div>

                    </div>
                </a>

                <div class="navbar-dropdown profil-menu">
                    <div class="navbar-item">
                        <b>
                            {{ $user->name }}
                        </b>
                    </div>
                    

                    <a href="{{ route("profile", $user) }}" class="navbar-item">
                        Профиль
                    </a>
                    @if(session()->has("oldUser".$user->getKey()))
                        <a href="{{ route("userLogout", $user) }}" class="navbar-item">
                            Вернуться <br> 
                            в свой профиль
                        </a>
                    @endif

                    <a href="" class="navbar-item">
                        Автосохранения
                    </a>

                    <div class="navbar-item is-flex">
                        <button  class='button is-outlined toggle-dark-style'>
                            @if($user->dark_style)
                                <span class="material-icons">
                                    wb_sunny
                                </span>
                            @else
                                <span class="material-icons">
                                    nights_stay
                                </span>
                            @endif
                        </button>
                    </div>

                    <a href="{{ route("logout") }}" class="navbar-item">
                        Выход
                    </a>
                </div>

            </div>
        </div>
    </div>
</nav>
