console.log('scripts.js загружен');

// Функции для работы с опросами

// Функция для обновления прогресс-бара для шкалы
function updateScaleProgress(value, progressBar) {
    const percentage = (value / 10) * 100;
    progressBar.style.width = `${percentage}%`;
    progressBar.setAttribute('aria-valuenow', value);
}

// Функция для обработки изменения значения шкалы
function handleScaleChange(event) {
    const value = event.target.value;
    const progressBar = event.target.closest('.scale-container').querySelector('.progress-bar');
    updateScaleProgress(value, progressBar);
}

// Функция для переключения видимости поля кода доступа
function toggleAccessCodeVisibility() {
    const isPublicCheckbox = document.getElementById('is_public');
    const accessCodeContainer = document.getElementById('access-code-container');
    
    if (isPublicCheckbox && accessCodeContainer) {
        if (isPublicCheckbox.checked) {
            accessCodeContainer.style.display = 'none';
        } else {
            accessCodeContainer.style.display = 'block';
        }
    }
}

// Функция для обработки изменения типа вопроса
function handleQuestionTypeChange() {
    const typeSelect = document.getElementById('type');
    const optionsContainer = document.getElementById('options-container');
    const scaleContainer = document.getElementById('scale-container');
    
    if (typeSelect && optionsContainer && scaleContainer) {
        const selectedType = typeSelect.value;
        
        // Скрываем все контейнеры специфичных настроек
        optionsContainer.classList.add('d-none');
        scaleContainer.classList.add('d-none');
        
        // Показываем нужный контейнер в зависимости от типа
        if (selectedType === 'single_choice' || selectedType === 'multiple_choice') {
            optionsContainer.classList.remove('d-none');
        } else if (selectedType === 'scale') {
            scaleContainer.classList.remove('d-none');
        }
    }
}

// Функция для обработки навигации по вопросам в опросе
function initSurveyNavigation() {
    const form = document.getElementById('survey-form');
    const questions = document.querySelectorAll('.question');
    const progressBar = document.querySelector('.progress-bar');
    
    if (form && questions.length > 0) {
        let currentQuestion = 0;
        const totalQuestions = questions.length;
        
        // Обработчики кнопок навигации
        document.querySelectorAll('.next-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const currentQuestionEl = questions[currentQuestion];
                
                // Проверка заполнения обязательных полей
                const requiredInputs = currentQuestionEl.querySelectorAll('input[required], textarea[required]');
                let isValid = true;
                
                requiredInputs.forEach(input => {
                    if (input.type === 'radio') {
                        const name = input.name;
                        const checked = currentQuestionEl.querySelector(`input[name="${name}"]:checked`);
                        if (!checked) isValid = false;
                    } else if (!input.value.trim()) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    alert('Пожалуйста, ответьте на обязательные вопросы');
                    return;
                }
                
                // Переход к следующему вопросу
                questions[currentQuestion].classList.add('d-none');
                currentQuestion++;
                questions[currentQuestion].classList.remove('d-none');
                
                // Обновление прогресс-бара
                if (progressBar) {
                    const progress = ((currentQuestion + 1) / totalQuestions) * 100;
                    progressBar.style.width = `${progress}%`;
                    progressBar.setAttribute('aria-valuenow', progress);
                }
            });
        });
        
        // Обработчики кнопок "Назад"
        document.querySelectorAll('.prev-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                questions[currentQuestion].classList.add('d-none');
                currentQuestion--;
                questions[currentQuestion].classList.remove('d-none');
                
                // Обновление прогресс-бара
                if (progressBar) {
                    const progress = ((currentQuestion + 1) / totalQuestions) * 100;
                    progressBar.style.width = `${progress}%`;
                    progressBar.setAttribute('aria-valuenow', progress);
                }
            });
        });
    }
}

// Функция для инициализации полноэкранного меню
function initFullscreenMenu() {
    const menuToggleBtn = document.getElementById('menuToggleBtn');
    const menuCloseBtn = document.getElementById('menuCloseBtn');
    const fullscreenMenu = document.getElementById('fullscreenMenu');
    const menuLinks = document.querySelectorAll('.fullscreen-menu-nav a');
    
    if (menuToggleBtn && menuCloseBtn && fullscreenMenu) {
        // Открыть меню
        menuToggleBtn.addEventListener('click', () => {
            fullscreenMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        // Закрыть меню
        menuCloseBtn.addEventListener('click', closeMenu);
        
        // Закрыть меню при клике на ссылку
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
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
        
        // Анимация для меню-кнопки
        menuToggleBtn.addEventListener('mouseover', function() {
            this.querySelector('.bar:nth-child(1)').style.width = '20px';
            this.querySelector('.bar:nth-child(2)').style.width = '15px';
        });
        
        menuToggleBtn.addEventListener('mouseout', function() {
            this.querySelector('.bar:nth-child(1)').style.width = '';
            this.querySelector('.bar:nth-child(2)').style.width = '';
        });
    }
}

// Функция закрытия меню
function closeMenu() {
    const fullscreenMenu = document.getElementById('fullscreenMenu');
    if (fullscreenMenu) {
        fullscreenMenu.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Функция для инициализации обработчиков копирования
function initClipboardHandlers() {
    const copyButtons = document.querySelectorAll('.copy-link');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const text = this.getAttribute('data-clipboard-text');
            navigator.clipboard.writeText(text).then(() => {
                // Временно меняем иконку для обратной связи
                const icon = this.querySelector('i');
                const originalClass = icon.className;
                icon.className = 'fas fa-check';
                
                setTimeout(() => {
                    icon.className = originalClass;
                }, 2000);
                
                // Показываем уведомление
                const tooltip = document.createElement('div');
                tooltip.className = 'position-fixed bottom-0 end-0 p-3';
                tooltip.innerHTML = `
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <i class="fas fa-link me-2"></i>
                            <strong class="me-auto">Ссылка скопирована</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Ссылка скопирована в буфер обмена
                        </div>
                    </div>
                `;
                
                document.body.appendChild(tooltip);
                
                // Удаляем уведомление через 3 секунды
                setTimeout(() => {
                    tooltip.remove();
                }, 3000);
            });
        });
    });
}

// Функции для анимации элементов сайта

// Анимация появления элементов при прокрутке
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    // Функция проверки видимости элемента
    function checkVisibility() {
        animatedElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150; // Расстояние до появления элемента
            
            if (elementTop < window.innerHeight - elementVisible) {
                // Добавляем класс для анимации
                element.classList.add('animate-active');
                element.classList.add('active');
                
                // Добавляем задержку для последовательного появления
                const index = Array.from(animatedElements).indexOf(element);
                element.style.transitionDelay = `${index * 0.1}s`;
            }
        });
    }
    
    // Добавляем обработчик прокрутки
    window.addEventListener('scroll', checkVisibility);
    
    // Проверяем видимость при загрузке страницы
    setTimeout(checkVisibility, 300); // Добавляем небольшую задержку для начала анимации
}

// Анимация загрузки страницы
function initPageTransitions() {
    // Добавляем класс для анимации загрузки
    document.body.classList.add('page-loaded');
        
    // Анимация перехода между страницами
    document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"]):not([href^="mailto:"])').forEach(link => {
        link.addEventListener('click', function(e) {
            // Пропускаем ссылки с модификаторами или специальные ссылки
            if (e.ctrlKey || e.metaKey || e.shiftKey || this.hasAttribute('data-no-transition')) {
                return;
            }
                
            const href = this.getAttribute('href');
            if (href && href !== '#' && !href.startsWith('javascript:')) {
                e.preventDefault();
                    
                // Добавляем анимацию ухода со страницы
                document.body.classList.add('page-transition-out');
                    
                // Переходим на новую страницу после завершения анимации
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            }
        });
    });
}

// Анимация карточек и блоков
function initCardAnimations() {
    // Анимация при наведении на карточки
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('card-hover');
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('card-hover');
        });
    });
    
    // Анимация появления карточек в сетке
    document.querySelectorAll('.card-grid .card').forEach((card, index) => {
        // Добавляем задержку для последовательного появления
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('card-animated');
    });
}

// Анимация кнопок и интерактивных элементов
function initButtonAnimations() {
    // Анимация кнопок при нажатии
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('mousedown', function() {
            this.classList.add('btn-pressed');
        });
        
        button.addEventListener('mouseup', function() {
            this.classList.remove('btn-pressed');
        });
        
        button.addEventListener('mouseleave', function() {
            this.classList.remove('btn-pressed');
        });
    });
    
    // Добавляем эффект пульсации для кнопок действия
    document.querySelectorAll('.btn-primary, .btn-success').forEach(button => {
        button.classList.add('btn-pulse');
    });
}

// Анимация графиков и диаграмм
function initChartAnimations() {
    // Добавляем анимацию для графиков и диаграмм
    document.querySelectorAll('.chart-container').forEach(container => {
        container.classList.add('chart-animated');
    });
    
    // Анимация прогресс-баров
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const value = bar.getAttribute('aria-valuenow');
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.transition = 'width 1s ease-in-out';
            bar.style.width = `${value}%`;
        }, 100);
    });
}

// Функция для проверки, был ли пройден опрос
function checkIfSurveyCompleted(surveyCode) {
    try {
        const completedSurveys = JSON.parse(localStorage.getItem('completedSurveys')) || {};
        return completedSurveys[surveyCode];
    } catch (error) {
        console.error('Ошибка при проверке статуса опроса:', error);
        return false;
    }
}

// Функция для сохранения информации о пройденном опросе
function markSurveyAsCompleted(surveyCode) {
    try {
        const completedSurveys = JSON.parse(localStorage.getItem('completedSurveys')) || {};
        completedSurveys[surveyCode] = {
            completedAt: new Date().toISOString(),
            status: 'completed'
        };
        localStorage.setItem('completedSurveys', JSON.stringify(completedSurveys));
    } catch (error) {
        console.error('Ошибка при сохранении статуса опроса:', error);
    }
}

// Функция для отправки формы
async function submitSurveyForm(form) {
    try {
        console.log('Начинаем отправку формы...');
        
        // Получаем CSRF токен
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF токен получен:', csrfToken);
        
        // Создаем объект FormData
        const formData = new FormData(form);
        
        // Добавляем CSRF токен в formData
        formData.append('_token', csrfToken);
        
        // Добавляем флаг автоматической отправки
        formData.append('auto_submit', '1');

        // Получаем URL для отправки из формы
        const submitUrl = form.getAttribute('action');
        console.log('URL для отправки:', submitUrl);

        // Получаем код опроса из URL
        const surveyCode = submitUrl.split('/').slice(-2)[0];
        console.log('Код опроса:', surveyCode);

        console.log('Отправляем запрос на сервер...');
        
        // Отправляем запрос с помощью fetch
        const response = await fetch(submitUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            credentials: 'same-origin'
        });

        console.log('Получен ответ от сервера:', response.status);
        
        if (response.ok) {
            console.log('Форма успешно отправлена');
            
            // Сохраняем информацию о прохождении опроса
            markSurveyAsCompleted(surveyCode);
            
            // Формируем URL страницы с ответами
            const baseUrl = window.location.origin;
            const responsesUrl = `${baseUrl}/s/${surveyCode}/responses`;
            console.log('Переходим на страницу:', responsesUrl);
            
            // Пробуем различные способы перенаправления
            try {
                window.location.assign(responsesUrl);
            } catch (e) {
                console.log('Ошибка при использовании location.assign, пробуем location.href');
                try {
                    window.location.href = responsesUrl;
                } catch (e2) {
                    console.log('Ошибка при использовании location.href, пробуем location.replace');
                    window.location.replace(responsesUrl);
                }
            }
        } else {
            const errorText = await response.text();
            console.error('Ошибка при отправке формы. Статус:', response.status, 'Текст ошибки:', errorText);
            alert('Произошла ошибка при отправке формы. Пожалуйста, попробуйте еще раз. Код ошибки: ' + response.status);
        }
    } catch (error) {
        console.error('Ошибка при отправке формы:', error);
        alert('Произошла ошибка при отправке формы. Пожалуйста, проверьте подключение к интернету и попробуйте еще раз.');
    }
}

// Функция для инициализации таймеров опроса
function initSurveyTimers() {
    console.log('Инициализация таймера...');
    
    // Находим все вопросы и прогресс-бар
    const questions = document.querySelectorAll('.question');
    const progressBar = document.querySelector('.progress-bar');
    const totalQuestions = questions.length;
    let currentQuestionIndex = 0;

    // Функция обновления прогресс-бара
    function updateProgressBar() {
        if (progressBar) {
            // Прогресс считаем как (текущий_вопрос + 1) / всего_вопросов * 100
            const progress = Math.round(((currentQuestionIndex + 1) / totalQuestions) * 100);
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
            progressBar.textContent = `${progress}%`;
            console.log('Прогресс обновлен:', progress + '%');
        }
    }

    // Функция для перехода к следующему вопросу
    function moveToNextQuestion() {
        // Скрываем текущий вопрос
        questions[currentQuestionIndex].classList.add('d-none');
        questions[currentQuestionIndex].classList.remove('active');
        
        // Переходим к следующему вопросу
        currentQuestionIndex++;
        
        // Если есть следующий вопрос
        if (currentQuestionIndex < totalQuestions) {
            // Показываем следующий вопрос
            questions[currentQuestionIndex].classList.remove('d-none');
            questions[currentQuestionIndex].classList.add('active');
            
            // Обновляем прогресс
            updateProgressBar();
            
            // Запускаем таймер для следующего вопроса
            startQuestionTimer(questions[currentQuestionIndex]);
        } else {
            // Если это был последний вопрос, отправляем форму
            const form = document.getElementById('survey-form');
            if (form) {
                submitSurveyForm(form);
            }
        }
    }

    // Функция для перехода к предыдущему вопросу
    function moveToPreviousQuestion() {
        // Скрываем текущий вопрос
        questions[currentQuestionIndex].classList.add('d-none');
        questions[currentQuestionIndex].classList.remove('active');
        
        // Переходим к предыдущему вопросу
        currentQuestionIndex--;
        
        // Показываем предыдущий вопрос
        questions[currentQuestionIndex].classList.remove('d-none');
        questions[currentQuestionIndex].classList.add('active');
        
        // Обновляем прогресс
        updateProgressBar();
        
        // Запускаем таймер для предыдущего вопроса
        startQuestionTimer(questions[currentQuestionIndex]);
    }

    // Функция для запуска таймера отдельного вопроса
    function startQuestionTimer(questionElement) {
        // Находим элемент таймера в вопросе
        const timerElement = questionElement.querySelector('.question-timer');
        if (!timerElement) return;

        // Получаем время из атрибута
        const timeLimit = parseInt(timerElement.getAttribute('data-time')) || 300;
        let timeLeft = timeLimit;

        // Очищаем предыдущий интервал, если он существует
        if (window.currentQuestionInterval) {
            clearInterval(window.currentQuestionInterval);
        }

        // Функция обновления таймера
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            // Форматируем время
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            // Если время истекло
            if (timeLeft <= 0) {
                clearInterval(window.currentQuestionInterval);
                moveToNextQuestion();
            }
            
            timeLeft--;
        }

        // Запускаем интервал обновления таймера
        window.currentQuestionInterval = setInterval(updateTimer, 1000);
        
        // Первоначальное обновление таймера
        updateTimer();
    }

    // Инициализация первого вопроса
    if (questions.length > 0) {
        // Скрываем все вопросы кроме первого
        questions.forEach((question, index) => {
            if (index === 0) {
                question.classList.remove('d-none');
                question.classList.add('active');
            } else {
                question.classList.add('d-none');
                question.classList.remove('active');
            }
        });

        // Устанавливаем начальный прогресс
        updateProgressBar();

        // Запускаем таймер для первого вопроса
        startQuestionTimer(questions[0]);
    }

    // Добавляем обработчики для кнопок навигации
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Проверяем валидацию текущего вопроса
            const currentQuestion = questions[currentQuestionIndex];
            const requiredInputs = currentQuestion.querySelectorAll('input[required], textarea[required]');
            let isValid = true;

            requiredInputs.forEach(input => {
                if (input.type === 'radio') {
                    const name = input.name;
                    const checked = currentQuestion.querySelector(`input[name="${name}"]:checked`);
                    if (!checked) isValid = false;
                } else if (!input.value.trim()) {
                    isValid = false;
                }
            });

            if (!isValid) {
                alert('Пожалуйста, ответьте на обязательные вопросы');
                return;
            }

            // Очищаем текущий интервал при ручном переходе
            if (window.currentQuestionInterval) {
                clearInterval(window.currentQuestionInterval);
            }

            moveToNextQuestion();
        });
    });

    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Очищаем текущий интервал при ручном переходе
            if (window.currentQuestionInterval) {
                clearInterval(window.currentQuestionInterval);
            }
            
            moveToPreviousQuestion();
        });
    });
}

// Инициализация всех компонентов при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем, находимся ли мы на странице опроса
    const surveyForm = document.getElementById('survey-form');
    if (surveyForm) {
        const surveyCode = surveyForm.getAttribute('action').split('/').slice(-2)[0];
        if (checkIfSurveyCompleted(surveyCode)) {
            // Если опрос уже пройден, перенаправляем на страницу с ответами
            const baseUrl = window.location.origin;
            window.location.replace(`${baseUrl}/s/${surveyCode}/responses`);
            return;
        }
    }

    // Инициализация прогресс-баров для шкал
    document.querySelectorAll('.scale-input').forEach(input => {
        const progressBar = input.closest('.scale-container').querySelector('.progress-bar');
        if (progressBar) {
            updateScaleProgress(input.value, progressBar);
            input.addEventListener('input', handleScaleChange);
        }
    });
    
    // Инициализация переключателя видимости кода доступа
    const isPublicCheckbox = document.getElementById('is_public');
    if (isPublicCheckbox) {
        toggleAccessCodeVisibility();
        isPublicCheckbox.addEventListener('change', toggleAccessCodeVisibility);
    }
    
    // Инициализация обработчика изменения типа вопроса
    const typeSelect = document.getElementById('type');
    if (typeSelect) {
        handleQuestionTypeChange();
        typeSelect.addEventListener('change', handleQuestionTypeChange);
    }
    
    // Инициализация ползунка прозрачности
    const opacitySlider = document.getElementById('design[image_opacity]');
    const opacityValue = document.getElementById('opacity-value');
    if (opacitySlider && opacityValue) {
        opacitySlider.addEventListener('input', function() {
            opacityValue.textContent = this.value;
        });
    }
    
    // Инициализация навигации по опросу
    initSurveyNavigation();
    
    // Инициализация полноэкранного меню
    initFullscreenMenu();
    
    // Инициализация обработчиков копирования
    initClipboardHandlers();
    
    // Инициализация анимаций
    initScrollAnimations();
    initPageTransitions();
    initCardAnimations();
    initButtonAnimations();
    initChartAnimations();
    
    // Инициализация таймеров
    initSurveyTimers();
}); 