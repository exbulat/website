/**
 * Модуль доступности для слабовидящих пользователей
 * 
 * Обеспечивает:
 * - Переключение в режим высокого контраста
 * - Изменение размера шрифта с помощью ползунка
 * - Инверсию цветов
 * - Сохранение настроек в localStorage
 */
document.addEventListener('DOMContentLoaded', function() {
    // Создание панели доступности
    createAccessibilityPanel();
    
    // Загрузка сохраненных настроек
    loadAccessibilitySettings();
    
    // Добавление обработчиков событий
    attachEventListeners();
    
    // Проверка на мобильное устройство
    checkMobileDevice();
});

/**
 * Проверка на мобильное устройство и адаптация панели
 */
function checkMobileDevice() {
    const isMobile = window.innerWidth <= 767;
    const container = document.getElementById('accessibilityContainer');
    
    if (isMobile && container) {
        // Добавляем класс для мобильных устройств
        container.classList.add('mobile-view');
        
        // Проверяем наличие тач-событий
        const hasTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        if (hasTouch) {
            // Добавляем обработчики для тач-устройств
            const panel = document.querySelector('.accessibility-panel');
            if (panel) {
                // Обработка свайпа вниз для закрытия панели
                let startY = 0;
                let endY = 0;
                
                panel.addEventListener('touchstart', function(e) {
                    startY = e.touches[0].clientY;
                }, false);
                
                panel.addEventListener('touchmove', function(e) {
                    endY = e.touches[0].clientY;
                }, false);
                
                panel.addEventListener('touchend', function(e) {
                    // Если свайп вниз и панель открыта
                    if (endY - startY > 100 && panel.classList.contains('open')) {
                        panel.classList.remove('open');
                    }
                }, false);
            }
        }
    }
    
    // Обновляем при изменении размера окна
    window.addEventListener('resize', function() {
        checkMobileDevice();
    });
}

/**
 * Создание панели доступности и добавление её на страницу
 */
function createAccessibilityPanel() {
    // Проверяем, находимся ли мы в полноэкранном меню
    const isInFullscreenMenu = document.querySelector('.fullscreen-menu-container.active') !== null;
    
    // Проверяем, является ли устройство мобильным
    const isMobile = window.innerWidth <= 767;
    
    // Если мы в полноэкранном меню или на мобильном устройстве, не создаем панель доступности
    if (isInFullscreenMenu || isMobile) {
        return;
    }
    
    // Удаляем существующую панель, если она есть
    const existingContainer = document.querySelector('.accessibility-container');
    if (existingContainer) {
        existingContainer.remove();
    }
    
    // Создаем контейнер для элементов доступности
    const accessibilityContainer = document.createElement('div');
    accessibilityContainer.className = 'accessibility-container';
    accessibilityContainer.id = 'accessibilityContainer';
    
    // Создаем кнопку-триггер для вызова панели
    const accessibilityTrigger = document.createElement('div');
    accessibilityTrigger.className = 'accessibility-trigger';
    accessibilityTrigger.id = 'accessibilityTrigger';
    accessibilityTrigger.setAttribute('aria-label', 'Открыть настройки доступности');
    accessibilityTrigger.setAttribute('tabindex', '0');
    accessibilityTrigger.setAttribute('role', 'button');
    accessibilityTrigger.innerHTML = `<i class="fas fa-universal-access"></i>`;
    
    // Создаем главную панель доступности
    const accessibilityPanel = document.createElement('div');
    accessibilityPanel.className = 'accessibility-panel';
    accessibilityPanel.setAttribute('aria-label', 'Настройки доступности');
    accessibilityPanel.innerHTML = `
        <div class="accessibility-header">
            <h3>Настройки доступности</h3>
            <button class="accessibility-close" aria-label="Закрыть настройки">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="accessibility-button" data-feature="accessibility-mode">
            <div>
                <i class="fas fa-eye"></i>
                <span>Версия для слабовидящих</span>
            </div>
            <div class="accessibility-toggle"></div>
        </div>
        
        <div class="accessibility-slider-container">
            <div class="accessibility-slider-header">
                <i class="fas fa-text-height"></i>
                <span>Размер шрифта</span>
                <span class="font-size-value">100%</span>
            </div>
            <input type="range" min="100" max="200" value="100" class="accessibility-slider" id="font-size-slider">
        </div>
        
        <div class="accessibility-button" data-feature="color-invert">
            <div>
                <i class="fas fa-adjust"></i>
                <span>Инверсия цветов</span>
            </div>
            <div class="accessibility-toggle"></div>
        </div>
        
        <div class="mt-3">
            <button class="btn btn-sm btn-outline-secondary w-100" id="reset-accessibility">
                <i class="fas fa-undo"></i> Сбросить настройки
            </button>
        </div>
    `;
    
    // Добавляем элементы в правильном порядке
    accessibilityContainer.appendChild(accessibilityTrigger); // Сначала триггер
    accessibilityContainer.appendChild(accessibilityPanel);   // Затем панель
    
    // Добавляем контейнер на страницу
    document.body.appendChild(accessibilityContainer);
}

/**
 * Применяет размер шрифта к документу
 * @param {number} size - Размер шрифта в процентах
 */
function applyFontSize(size) {
    // Удаляем все предыдущие стили размера шрифта
    const existingStyle = document.getElementById('accessibility-font-size');
    if (existingStyle) {
        existingStyle.remove();
    }
    
    // Создаем новый элемент стиля
    const style = document.createElement('style');
    style.id = 'accessibility-font-size';
    style.innerHTML = `
        html {
            font-size: ${size}% !important;
        }
    `;
    document.head.appendChild(style);
}

/**
 * Добавление обработчиков событий к кнопкам панели доступности
 */
function attachEventListeners() {
    // Получаем ссылки на элементы
    const accessibilityTrigger = document.getElementById('accessibilityTrigger');
    const accessibilityPanel = document.querySelector('.accessibility-panel');
    const closeButton = document.querySelector('.accessibility-close');
    const accessibilityButtons = document.querySelectorAll('.accessibility-button');
    const fontSizeSlider = document.getElementById('font-size-slider');
    const fontSizeValue = document.querySelector('.font-size-value');
    const resetButton = document.getElementById('reset-accessibility');
    
    if (!accessibilityTrigger || !accessibilityPanel || !closeButton) {
        console.error('Не удалось найти элементы панели доступности');
        return;
    }
    
    // Переключение режима доступности
    accessibilityButtons.forEach(button => {
        button.addEventListener('click', function() {
            const feature = this.getAttribute('data-feature');
            const toggle = this.querySelector('.accessibility-toggle');
            
            // Переключение активного состояния
            toggle.classList.toggle('active');
            
            // Сохранение и применение настроек
            const isActive = toggle.classList.contains('active');
            
            // Применяем класс к body независимо от основного режима
            document.body.classList.toggle(feature, isActive);
            
            // Сохранение настроек
            localStorage.setItem(feature, isActive ? 'true' : 'false');
        });
    });
    
    // Добавляем обработчик для ползунка размера шрифта
    fontSizeSlider.addEventListener('input', function() {
        const value = this.value;
        fontSizeValue.textContent = value + '%';
        applyFontSize(value);
        localStorage.setItem('font-size-value', value);
    });
    
    // Сброс настроек
    resetButton.addEventListener('click', function() {
        // Сброс всех переключателей
        document.querySelectorAll('.accessibility-toggle.active').forEach(toggle => {
            toggle.classList.remove('active');
        });
        
        // Удаление классов с body
        document.body.classList.remove('accessibility-mode', 'color-invert');
        
        // Сброс размера шрифта
        fontSizeSlider.value = 100;
        fontSizeValue.textContent = '100%';
        applyFontSize(100);
        
        // Очистка localStorage
        localStorage.removeItem('accessibility-mode');
        localStorage.removeItem('color-invert');
        localStorage.removeItem('font-size-value');
    });
    
    // Добавляем поддержку клавиатуры
    accessibilityTrigger.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            accessibilityPanel.classList.add('open');
        }
    });
    
    // Открытие панели при нажатии на триггер
    accessibilityTrigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Нажатие на кнопку доступности');
        // Скрываем кнопку триггера перед открытием панели
        accessibilityTrigger.classList.add('hidden');
        
        // Небольшая задержка для анимации скрытия кнопки
        setTimeout(() => {
            // Открываем панель после скрытия кнопки
            accessibilityPanel.classList.add('open');
            // Прокручиваем панель в начало при открытии
            accessibilityPanel.scrollTop = 0;
        }, 50);
    });
    
    // Закрытие панели при нажатии на кнопку закрытия
    closeButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        accessibilityPanel.classList.remove('open');
        
        // Показываем кнопку триггера при закрытии панели с задержкой
        setTimeout(() => {
            accessibilityTrigger.classList.remove('hidden');
        }, 300); // Задержка равна длительности анимации закрытия панели
    });
    
    // Закрытие панели при клике вне панели
    document.addEventListener('click', function(e) {
        if (!accessibilityPanel.contains(e.target) && 
            !accessibilityTrigger.contains(e.target) && 
            accessibilityPanel.classList.contains('open')) {
            // Закрываем панель
            accessibilityPanel.classList.remove('open');
            
            // Показываем кнопку триггера при закрытии панели с задержкой
            setTimeout(() => {
                accessibilityTrigger.classList.remove('hidden');
            }, 300); // Задержка равна длительности анимации закрытия панели
        }
    });
}

/**
 * Загрузка сохраненных настроек из localStorage
 */
function loadAccessibilitySettings() {
    // Проверка сохраненных настроек переключателей
    const features = ['accessibility-mode', 'color-invert'];
    
    features.forEach(feature => {
        const isActive = localStorage.getItem(feature) === 'true';
        
        if (isActive) {
            // Применение классов к body
            document.body.classList.add(feature);
            
            // Включение соответствующего переключателя
            const toggle = document.querySelector(`.accessibility-button[data-feature="${feature}"] .accessibility-toggle`);
            if (toggle) {
                toggle.classList.add('active');
            }
        }
    });
    
    // Загрузка сохраненного размера шрифта
    const savedFontSize = localStorage.getItem('font-size-value');
    if (savedFontSize) {
        const fontSizeValue = parseInt(savedFontSize);
        document.getElementById('font-size-slider').value = fontSizeValue;
        document.querySelector('.font-size-value').textContent = fontSizeValue + '%';
        applyFontSize(fontSizeValue);
    }
}
