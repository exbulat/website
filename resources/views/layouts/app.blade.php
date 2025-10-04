<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SurveyMaster')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Версия для слабовидящих -->
    <link rel="stylesheet" href="{{ asset('public/css/accessibility.css') }}">
    
    <!-- Анимации -->
    <link rel="stylesheet" href="{{ asset('public/css/animations.css') }}">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/public/css/styles.css">
    
    @yield('styles')
</head>
<body>
    <div id="app">
        <!-- Новая навигационная панель с кнопкой меню -->
        <header class="bg-white shadow-sm py-2">
            <div class="container">
                <div class="navbar-brand-container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <i class="fas fa-poll me-2"></i>Survey Master
                    </a>
                    
                    <div class="quick-nav d-none d-md-flex">
                        <a href="{{ route('public.surveys.index') }}" class="quick-nav-link">
                            <i class="fas fa-globe me-1"></i>Публичные опросы
                        </a>
                        @auth
                        <a href="{{ route('surveys.index') }}" class="quick-nav-link">
                            <i class="fas fa-list me-1"></i>Мои опросы
                        </a>
                        @endauth
                    </div>
                    
                    <div class="d-flex align-items-center">
                        @auth
                        <a href="{{ route('notifications.index') }}" class="header-icon me-3 position-relative">
                            <i class="fas fa-bell"></i>
                            @php
                                $unreadCount = Auth::user()->notifications()->where('read_at', null)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="notification-badge">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('profile.show') }}" class="header-avatar me-3">
                            <i class="fas fa-user"></i>
                        </a>
                        @endauth
                        <button class="menu-toggle-btn" type="button" id="menuToggleBtn">
                            <span class="bar"></span>
                            <span class="bar"></span>
                            <span class="bar"></span>
                        </button>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Полноэкранное меню -->
        <div class="fullscreen-menu-container" id="fullscreenMenu">
            <button class="menu-close-btn" id="menuCloseBtn">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="fullscreen-menu" id="scrollableMenu">
                @auth
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3>{{ Auth::user()->name }}</h3>
                    </div>
                @endauth
                
                <nav class="fullscreen-menu-nav">
                    @auth
                        <div class="menu-column">
                            <h4>Навигация</h4>
                            <a href="{{ url('/') }}" style="transition-delay: 0.1s">
                                <i class="fas fa-home me-2"></i>Главная
                            </a>
                            <a href="{{ route('public.surveys.index') }}" style="transition-delay: 0.2s">
                                <i class="fas fa-globe me-2"></i>Публичные опросы
                            </a>
                        </div>
                        
                        <div class="menu-column">
                            <h4>Опросы</h4>
                            <a href="{{ route('surveys.index') }}" style="transition-delay: 0.1s">
                                <i class="fas fa-list me-2"></i>Мои опросы
                            </a>
                            <a href="{{ route('surveys.history') }}" style="transition-delay: 0.2s">
                                <i class="fas fa-history me-2"></i>История опросов
                            </a>
                            <a href="{{ route('surveys.archive.index') }}" style="transition-delay: 0.3s">
                                <i class="fas fa-archive me-2"></i>Архив опросов
                            </a>
                            <a href="{{ route('surveys.create') }}" style="transition-delay: 0.4s">
                                <i class="fas fa-plus-circle me-2"></i>Создать новый
                            </a>
                        </div>
                        
                        <div class="menu-column">
                            <h4>Личный кабинет</h4>
                            <a href="{{ route('profile.show') }}" style="transition-delay: 0.1s">
                                <i class="fas fa-user me-2"></i>Мой профиль
                            </a>
                            <a href="{{ route('notifications.index') }}" style="transition-delay: 0.2s">
                                <i class="fas fa-bell me-2"></i>Уведомления
                                @php
                                    $unreadCount = Auth::user()->notifications()->where('read_at', null)->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="notification-badge">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" style="transition-delay: 0.25s">
                                <i class="fas fa-tachometer-alt me-2"></i>Админ-панель
                            </a>
                            @endif
                            <button type="button" class="menu-button" 
                               onclick="document.getElementById('logout-form').submit();" 
                               style="transition-delay: 0.3s">
                                <i class="fas fa-sign-out-alt me-2"></i>Выход
                            </button>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    @else
                        <div class="menu-column">
                            <h4>Навигация</h4>
                            <a href="{{ url('/') }}" style="transition-delay: 0.1s">
                                <i class="fas fa-home me-2"></i>Главная
                            </a>
                            <a href="{{ route('public.surveys.index') }}" style="transition-delay: 0.2s">
                                <i class="fas fa-globe me-2"></i>Публичные опросы
                            </a>
                        </div>
                        
                        <div class="menu-column">
                            <h4>Вход в систему</h4>
                            <a href="{{ route('login') }}" class="auth-button login-btn" style="transition-delay: 0.1s;">
                                <i class="fas fa-sign-in-alt me-2"></i>Вход
                            </a>
                            <a href="{{ route('register') }}" class="auth-button register-btn" style="transition-delay: 0.2s;">
                                <i class="fas fa-user-plus me-2"></i>Регистрация
                            </a>
                        </div>
                    @endif
                </nav>
            </div>
        </div>

        <main class="py-4">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            
            @yield('content')
        </main>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (для совместимости) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Полноэкранное меню скрипт -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggleBtn = document.getElementById('menuToggleBtn');
            const menuCloseBtn = document.getElementById('menuCloseBtn');
            const fullscreenMenu = document.getElementById('fullscreenMenu');
            const menuLinks = document.querySelectorAll('.fullscreen-menu-nav a');
            
            // Открыть меню
            menuToggleBtn.addEventListener('click', function() {
                // Открываем меню
                fullscreenMenu.classList.add('active');
                document.body.style.overflow = 'hidden'; // Блокируем прокрутку
                
                // Скрываем панель доступности, если она открыта
                const accessibilityContainer = document.getElementById('accessibilityContainer');
                if (accessibilityContainer) {
                    accessibilityContainer.style.display = 'none';
                }
                
                // Прокручиваем меню в начало при открытии
                if (window.innerWidth <= 767) {
                    setTimeout(() => {
                        fullscreenMenu.scrollTop = 0;
                    }, 10);
                }
            });
            
            // Закрыть меню
            menuCloseBtn.addEventListener('click', closeMenu);
            
            // Закрыть меню при клике на ссылку
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Не закрываем меню если это ссылка на выход
                    if (!this.hasAttribute('onclick')) {
                        closeMenu();
                    }
                });
            });
            
            // Закрыть меню при клике вне его содержимого
            fullscreenMenu.addEventListener('click', function(e) {
                if (e.target === fullscreenMenu) {
                    closeMenu();
                }
            });
            
            // Функция закрытия меню
            function closeMenu() {
                fullscreenMenu.classList.remove('active');
                document.body.style.overflow = ''; // Разблокируем прокрутку
                
                // Восстанавливаем отображение панели доступности
                const accessibilityContainer = document.getElementById('accessibilityContainer');
                if (accessibilityContainer) {
                    // Делаем небольшую задержку, чтобы анимация закрытия меню завершилась
                    setTimeout(() => {
                        accessibilityContainer.style.display = '';
                    }, 300);
                }
            }
            
            // Обработка тач-событий для мобильных устройств
            let touchStartY = 0;
            let touchEndY = 0;
            
            // Получаем ссылку на содержимое меню, которое должно прокручиваться
            const scrollableMenu = document.getElementById('scrollableMenu');
            
            if (scrollableMenu) {
                // Добавляем обработчики для тач-событий на содержимое меню
                scrollableMenu.addEventListener('touchstart', function(e) {
                    touchStartY = e.touches[0].clientY;
                }, { passive: true }); // Используем passive: true для производительности
                
                scrollableMenu.addEventListener('touchmove', function(e) {
                    touchEndY = e.touches[0].clientY;
                    const scrollTop = scrollableMenu.scrollTop;
                    const scrollHeight = scrollableMenu.scrollHeight;
                    const clientHeight = scrollableMenu.clientHeight;
                    
                    // Проверяем, находимся ли мы в начале или в конце прокрутки
                    const isAtTop = scrollTop <= 0;
                    const isAtBottom = scrollHeight - scrollTop <= clientHeight + 1;
                    
                    // Определяем направление прокрутки
                    const isScrollingDown = touchEndY < touchStartY;
                    const isScrollingUp = touchEndY > touchStartY;
                    
                    // Если мы в начале и пытаемся прокрутить вверх или в конце и пытаемся прокрутить вниз,
                    // то позволяем прокрутку страницы
                    if ((isAtTop && isScrollingUp) || (isAtBottom && isScrollingDown)) {
                        // Не делаем ничего, позволяем стандартную прокрутку
                        return;
                    }
                }, { passive: true }); // Используем passive: true для производительности
            }
            
            // Предотвращаем прокрутку фона при открытом меню
            fullscreenMenu.addEventListener('touchmove', function(e) {
                // Если клик не по содержимому меню, а по фону
                if (e.target === fullscreenMenu) {
                    e.preventDefault(); // Предотвращаем прокрутку фона
                }
            }, { passive: false });
            
            // Анимация для меню-кнопки
            menuToggleBtn.addEventListener('mouseover', function() {
                this.querySelector('.bar:nth-child(1)').style.width = '20px';
                this.querySelector('.bar:nth-child(2)').style.width = '15px';
            });
            
            menuToggleBtn.addEventListener('mouseout', function() {
                this.querySelector('.bar:nth-child(1)').style.width = '';
                this.querySelector('.bar:nth-child(2)').style.width = '';
            });
        });
    </script>
    
    <!-- Custom Scripts -->
    <script src="{{ asset('public/js/scripts.js') }}"></script>
    
    <!-- Оптимизации производительности -->
    <script src="{{ asset('public/js/performance-optimizations.js') }}"></script>
    
    <!-- Скрипт для версии слабовидящих -->
    <script src="{{ asset('public/js/accessibility.js') }}"></script>
    
    <!-- Page Specific Scripts -->
    @yield('scripts')
</body>
</html>
