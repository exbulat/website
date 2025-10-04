<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Админ-панель') | {{ config('app.name') }}</title>
    
    <!-- Стили Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Стили для админ-панели -->
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 65px;
            --topbar-height: 60px;
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
            --transition-duration: 0.3s;
        }
        
        /* Общие стили для анимаций */
        * {
            transition-timing-function: cubic-bezier(0.25, 0.1, 0.25, 1.0);
        }
        
        body {
            overflow-x: hidden;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            transition: all var(--transition-duration) ease-in-out;
            color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            will-change: width; /* Оптимизация для анимации */
            overflow-x: hidden; /* Предотвращение горизонтальной прокрутки при анимации */
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
            box-shadow: 0 0.1rem 0.5rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .sidebar.collapsed .nav-link {
            padding: 0.8rem 0;
            justify-content: center;
        }
        
        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-size: 1.2rem;
            font-weight: 800;
            color: white;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease-in-out;
            overflow: hidden; /* Предотвращаем переполнение при анимации */
        }
        
        .sidebar-brand a {
            color: white;
            text-decoration: none;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin: 1rem 0;
        }
        
        .sidebar-heading {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            padding: 0 1rem;
            font-weight: 700;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease-in-out;
            border-radius: 0.35rem;
            margin: 0.2rem 0.5rem;
            overflow: hidden; /* Предотвращаем переполнение при анимации */
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
            box-shadow: 0 0.125rem 0.25rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .nav-link i {
            margin-right: 0.5rem;
            font-size: 1rem;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.2rem;
            margin-left: 0; /* Центрирование иконок в свернутой панели */
            position: relative;
            left: -2px; /* Корректировка позиции */
        }
        
        .nav-link span {
            font-size: 0.9rem;
        }
        
        .sidebar .sidebar-brand-text,
        .sidebar .sidebar-heading,
        .sidebar .nav-link span {
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
            opacity: 1;
            transform: translateX(0);
            white-space: nowrap;
        }
        
        .sidebar.collapsed .sidebar-brand-text,
        .sidebar.collapsed .sidebar-heading,
        .sidebar.collapsed .nav-link span {
            opacity: 0;
            transform: translateX(-10px);
            display: none;
        }
        
        .page-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all var(--transition-duration) ease-in-out;
            padding: 1.5rem;
            will-change: margin-left; /* Оптимизация для анимации */
        }
        
        .page-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .topbar {
            height: var(--topbar-height);
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }
        
        .topbar .navbar-search {
            width: 30rem;
        }
        
        .topbar-divider {
            width: 0;
            border-right: 1px solid #e3e6f0;
            height: 2rem;
            margin: auto 1rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e3e6f0;
            background-color: #f8f9fc;
        }
        
        .card-header h6 {
            font-weight: 700;
            margin: 0;
        }
        
        .stat-card {
            border-left: 4px solid;
            border-radius: 0.25rem;
        }
        
        .stat-card.primary {
            border-left-color: var(--primary-color);
        }
        
        .stat-card.success {
            border-left-color: var(--secondary-color);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .stat-card.danger {
            border-left-color: var(--danger-color);
        }
        
        .stat-card.info {
            border-left-color: var(--info-color);
        }
        
        .content-container {
            padding: 1.5rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            
            .sidebar-brand-text,
            .sidebar-heading,
            .nav-link span {
                display: none;
            }
            
            .page-content {
                margin-left: var(--sidebar-collapsed-width);
            }
            
            .topbar .navbar-search {
                width: 100%;
            }
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }
        
        .dropdown-item.active, .dropdown-item:active {
            background-color: var(--primary-color);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Боковая панель -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span class="sidebar-brand-text">Админ-панель</span>
            </a>
        </div>
        
        <hr class="sidebar-divider">
        
        
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Дашборд</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Пользователи</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.surveys') }}" class="nav-link {{ request()->routeIs('admin.surveys*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-poll"></i>
                    <span>Опросы</span>
                </a>
            </li>
        </ul>
        
        <hr class="sidebar-divider">
        
        
        
        <ul class="nav flex-column">
            @if(auth()->user()->is_super_admin)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Логи активности</span>
                </a>
            </li>
            @endif
            
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link" target="_blank">
                    <i class="fas fa-fw fa-globe"></i>
                    <span>Перейти на сайт</span>
                </a>
            </li>
        </ul>
        
        <hr class="sidebar-divider">
        
        <!-- Стрелка удалена, теперь панель открывается при наведении -->
        <div class="hover-area" style="position: fixed; top: 0; left: 0; width: 10px; height: 100%; z-index: 99;"></div>
    </nav>
    
    <!-- Основной контент -->
    <div class="page-content" id="page-content">
        <!-- Верхняя панель -->
        <nav class="topbar">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            
            <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Поиск..." aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <ul class="navbar-nav ml-auto">
                <div class="topbar-divider"></div>
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                        <i class="fas fa-user-circle fa-fw"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Профиль
                        </a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item" onclick="document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Выход
                        </button>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        
        <!-- Контейнер для содержимого -->
        <div class="content-container">
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
            
            @yield('content')
        </div>
    </div>
    
    <!-- Скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Переключатель боковой панели
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarToggleTop = document.getElementById('sidebarToggleTop');
            const sidebar = document.getElementById('sidebar');
            const pageContent = document.getElementById('page-content');
            
            function toggleSidebar() {
                // Добавляем класс для анимации
                document.body.classList.add('sidebar-transitioning');
                
                // Переключаем классы
                sidebar.classList.toggle('collapsed');
                pageContent.classList.toggle('expanded');
                
                // Сохраняем состояние в localStorage
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                
                // Удаляем класс анимации после завершения перехода
                setTimeout(() => {
                    document.body.classList.remove('sidebar-transitioning');
                }, 300);
                
                // Добавляем плавную анимацию для контента
                const contentItems = document.querySelectorAll('.content-container > *');
                contentItems.forEach((item, index) => {
                    item.style.transition = `transform 0.3s ease ${index * 0.05}s, opacity 0.3s ease ${index * 0.05}s`;
                    item.style.transform = 'translateX(10px)';
                    item.style.opacity = '0.7';
                    
                    setTimeout(() => {
                        item.style.transform = 'translateX(0)';
                        item.style.opacity = '1';
                    }, 50);
                });
            }
            
            // Восстанавливаем состояние из localStorage
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                toggleSidebar();
            }
            
            // Добавляем обработчики событий для открытия/закрытия при наведении
            const hoverArea = document.querySelector('.hover-area');
            
            // Открытие при наведении на область или саму панель
            hoverArea.addEventListener('mouseenter', () => {
                if (sidebar.classList.contains('collapsed')) {
                    toggleSidebar();
                }
            });
            
            sidebar.addEventListener('mouseenter', () => {
                if (sidebar.classList.contains('collapsed')) {
                    toggleSidebar();
                }
            });
            
            // Закрытие при уходе с панели
            sidebar.addEventListener('mouseleave', () => {
                if (!sidebar.classList.contains('collapsed')) {
                    toggleSidebar();
                }
            });
            
            // Добавляем обработчик клика по пункту меню для закрытия панели
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    // Закрываем панель с небольшой задержкой
                    setTimeout(() => {
                        if (!sidebar.classList.contains('collapsed')) {
                            toggleSidebar();
                        }
                    }, 300);
                });
            });
            
            // Инициализация всплывающих подсказок
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
