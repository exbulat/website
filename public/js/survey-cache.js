/**
 * Модуль для кеширования ответов на опросы
 */

// Ключ для хранения ответов в localStorage
const SURVEY_RESPONSES_KEY = 'survey_responses';

// Функция для сохранения ответа на опрос
function saveSurveyResponse(surveyCode, response) {
    try {
        // Получаем текущие сохраненные ответы
        let responses = JSON.parse(localStorage.getItem(SURVEY_RESPONSES_KEY) || '{}');
        
        // Добавляем новый ответ
        responses[surveyCode] = {
            response: response,
            timestamp: new Date().toISOString()
        };
        
        // Сохраняем обновленные ответы
        localStorage.setItem(SURVEY_RESPONSES_KEY, JSON.stringify(responses));
        
        return true;
    } catch (error) {
        console.error('Ошибка при сохранении ответа:', error);
        return false;
    }
}

// Функция для проверки, отвечал ли пользователь на опрос
function hasUserResponded(surveyCode) {
    try {
        const responses = JSON.parse(localStorage.getItem(SURVEY_RESPONSES_KEY) || '{}');
        return !!responses[surveyCode];
    } catch (error) {
        console.error('Ошибка при проверке ответа:', error);
        return false;
    }
}

// Функция для получения ответа пользователя на опрос
function getUserResponse(surveyCode) {
    try {
        const responses = JSON.parse(localStorage.getItem(SURVEY_RESPONSES_KEY) || '{}');
        return responses[surveyCode] || null;
    } catch (error) {
        console.error('Ошибка при получении ответа:', error);
        return null;
    }
}

// Функция для блокировки формы и установки сохраненных ответов
function disableFormAndSetResponses(surveyForm, surveyCode) {
    const savedResponse = getUserResponse(surveyCode);
    if (!savedResponse) return;

    // Проверяем, существует ли уже уведомление
    if (!document.querySelector('.survey-response-alert')) {
        // Создаем контейнер для уведомления и кнопки
        const alertContainer = document.createElement('div');
        alertContainer.className = 'survey-response-alert';

        // Добавляем уведомление
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-info mb-3';
        alertDiv.innerHTML = '<i class="fas fa-info-circle me-2"></i>Вы уже ответили на этот опрос. Форма доступна только для просмотра.';
        alertContainer.appendChild(alertDiv);

        // Добавляем кнопку возврата на главную
        const homeButton = document.createElement('a');
        homeButton.href = '/';
        homeButton.className = 'btn btn-primary mb-4';
        homeButton.innerHTML = '<i class="fas fa-home me-2"></i>Вернуться на главную';
        alertContainer.appendChild(homeButton);

        // Вставляем контейнер перед формой
        surveyForm.parentNode.insertBefore(alertContainer, surveyForm);
    }

    // Блокируем все элементы формы
    const formElements = surveyForm.querySelectorAll('input, textarea, button, select');
    formElements.forEach(element => {
        element.disabled = true;
    });

    // Устанавливаем сохраненные ответы
    const response = savedResponse.response;
    for (let key in response) {
        if (key === '_token') continue; // Пропускаем CSRF токен

        const value = response[key];
        const elements = surveyForm.querySelectorAll(`[name="${key}"]`);
        
        elements.forEach(element => {
            if (element.type === 'radio' || element.type === 'checkbox') {
                if (Array.isArray(value)) {
                    element.checked = value.includes(element.value);
                } else {
                    element.checked = element.value === value;
                }
            } else {
                element.value = value;
            }
        });
    }

    // Скрываем кнопки навигации и отправки
    const buttons = surveyForm.querySelectorAll('.prev-btn, .next-btn, .submit-btn');
    buttons.forEach(button => {
        button.style.display = 'none';
    });

    // Показываем все вопросы сразу
    const questions = surveyForm.querySelectorAll('.question');
    questions.forEach(question => {
        question.classList.remove('d-none');
        question.classList.add('active');
    });

    // Устанавливаем прогресс-бар на 100%
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = '100%';
        progressBar.setAttribute('aria-valuenow', '100');
        progressBar.textContent = '100%';
    }
}

// Функция для предотвращения повторного прохождения опроса
function preventSurveyResubmission() {
    const surveyForm = document.getElementById('survey-form');
    if (!surveyForm) return;
    
    const surveyCode = surveyForm.getAttribute('data-survey-code');
    if (!surveyCode) return;
    
    // Если пользователь уже отвечал на этот опрос
    if (hasUserResponded(surveyCode)) {
        disableFormAndSetResponses(surveyForm, surveyCode);
        return;
    }
    
    // Обработчик отправки формы
    surveyForm.addEventListener('submit', function(e) {
        // Сохраняем ответы перед отправкой
        const formData = new FormData(this);
        const response = {};
        
        for (let [key, value] of formData.entries()) {
            response[key] = value;
        }
        
        saveSurveyResponse(surveyCode, response);
    });
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    preventSurveyResubmission();
});

// Дополнительная проверка при возврате на страницу
window.addEventListener('pageshow', function(event) {
    // Проверяем, загружена ли страница из кэша
    if (event.persisted) {
        const surveyForm = document.getElementById('survey-form');
        if (!surveyForm) return;
        
        const surveyCode = surveyForm.getAttribute('data-survey-code');
        if (!surveyCode) return;
        
        if (hasUserResponded(surveyCode)) {
            disableFormAndSetResponses(surveyForm, surveyCode);
        }
    }
}); 