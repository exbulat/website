/**
 * Радикальная оптимизация производительности для сайта опросов
 */

// Решение проблемы зависания при масштабировании
(function() {
    // Состояние масштабирования
    let isScaling = false;
    let scalingTimer = null;
    let lastWheelTime = 0;
    let wheelCount = 0;
    
    // Скрыть тяжелые элементы при масштабировании
    function hideHeavyElements() {
        // Скрыть все графики и анимации
        document.querySelectorAll('.chart-container, .animate-on-scroll, .result-card').forEach(el => {
            el.style.visibility = 'hidden';
        });
        
        // Скрыть тени и сложные эффекты
        document.body.classList.add('performance-mode');
    }
    
    // Показать скрытые элементы
    function showHeavyElements() {
        // Показать все графики и анимации
        document.querySelectorAll('.chart-container, .animate-on-scroll, .result-card').forEach(el => {
            el.style.visibility = '';
        });
        
        // Вернуть тени и эффекты
        document.body.classList.remove('performance-mode');
    }
    
    // Детектор масштабирования
    window.addEventListener('wheel', function(e) {
        // Проверяем, что это масштабирование (Ctrl + колесико)
        if (e.ctrlKey) {
            // Предотвращаем стандартное поведение браузера
            e.preventDefault();
            
            const now = Date.now();
            
            // Счетчик быстрых событий колесика
            if (now - lastWheelTime < 200) {
                wheelCount++;
            } else {
                wheelCount = 1;
            }
            lastWheelTime = now;
            
            // Если обнаружено быстрое масштабирование
            if (wheelCount > 3 && !isScaling) {
                isScaling = true;
                hideHeavyElements();
            }
            
            // Сброс таймера восстановления
            clearTimeout(scalingTimer);
            
            // Ручное масштабирование страницы
            const currentScale = document.body.style.zoom ? parseFloat(document.body.style.zoom) : 1;
            const delta = e.deltaY > 0 ? 0.9 : 1.1;
            const newScale = Math.max(0.1, Math.min(5, currentScale * delta));
            
            // Применяем масштаб
            document.body.style.zoom = newScale;
            
            // Таймер для восстановления элементов после масштабирования
            scalingTimer = setTimeout(function() {
                isScaling = false;
                wheelCount = 0;
                showHeavyElements();
                
                // Обновляем графики без анимации
                if (window.chartInstances) {
                    Object.values(window.chartInstances).forEach(chart => {
                        if (chart && typeof chart.update === 'function') {
                            chart.update('none');
                        }
                    });
                }
            }, 800);
        }
    }, { passive: false });
    
    // Отключаем тяжелые эффекты при низкой производительности
    function checkPerformance() {
        const startTime = performance.now();
        let count = 0;
        
        // Простой тест производительности
        for (let i = 0; i < 10000; i++) {
            count += Math.sqrt(i);
        }
        
        const endTime = performance.now();
        const duration = endTime - startTime;
        
        // Если тест занял больше 10мс, считаем устройство медленным
        if (duration > 10) {
            document.body.classList.add('low-performance-device');
        }
    }
    
    // Запускаем тест производительности при загрузке
    document.addEventListener('DOMContentLoaded', checkPerformance);
})();
