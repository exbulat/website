@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Получаем список пройденных опросов
        const completedSurveys = JSON.parse(localStorage.getItem('completedSurveys')) || {};
        
        // Проверяем каждую карточку опроса
        document.querySelectorAll('[data-survey-code]').forEach(card => {
            const surveyCode = card.getAttribute('data-survey-code');
            if (completedSurveys[surveyCode]) {
                // Если опрос пройден, меняем кнопку и добавляем метку
                const button = card.querySelector('.btn');
                if (button) {
                    button.textContent = 'Просмотреть ответы';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-secondary');
                    button.href = `/s/${surveyCode}/responses`;
                }
                
                // Добавляем метку о прохождении
                const badge = document.createElement('div');
                badge.className = 'position-absolute top-0 end-0 m-2 badge bg-success';
                badge.textContent = 'Пройден';
                card.style.position = 'relative';
                card.appendChild(badge);
            }
        });
    });
</script>
@endpush