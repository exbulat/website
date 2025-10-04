<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * Форма создания нового вопроса
     */
    public function create(Survey $survey)
    {
        Gate::authorize('update', $survey);
        
        $questionTypes = Question::getTypes();
        $position = $survey->questions()->count() + 1;
        
        return view('surveys.questions.create', compact('survey', 'questionTypes', 'position'));
    }

    /**
     * Сохранение нового вопроса
     */
    public function store(Request $request, Survey $survey)
    {
        Gate::authorize('update', $survey);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(Question::getTypes())),
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
            'position' => 'required|integer|min:1',
            'is_required' => 'boolean',
            'time_limit' => 'nullable|integer|min:1',
        ]);
        
        // Фильтруем пустые опции
        if (isset($validated['options']) && is_array($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], function($option) {
                return !empty($option);
            });
        }
        
        // Добавляем параметры шкалы, если тип вопроса - шкала
        if ($validated['type'] === 'scale') {
            if (!isset($validated['options'])) {
                $validated['options'] = [];
            }
            $validated['options']['min'] = $request->input('scale_min', 1);
            $validated['options']['max'] = $request->input('scale_max', 10);
        }
        
        $question = $survey->questions()->create($validated);
        
        if ($request->has('add_another')) {
            return redirect()->route('surveys.questions.create', $survey)
                ->with('success', 'Вопрос успешно добавлен! Добавьте еще один.');
        }
        
        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Вопрос успешно добавлен!');
    }

    /**
     * Форма редактирования вопроса
     */
    public function edit(Survey $survey, Question $question)
    {
        Gate::authorize('update', $survey);
        
        if ($question->survey_id !== $survey->id) {
            abort(404);
        }
        
        $questionTypes = Question::getTypes();
        
        return view('surveys.questions.edit', compact('survey', 'question', 'questionTypes'));
    }

    /**
     * Обновление вопроса
     */
    public function update(Request $request, Survey $survey, Question $question)
    {
        Gate::authorize('update', $survey);
        
        if ($question->survey_id !== $survey->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(Question::getTypes())),
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
            'position' => 'required|integer|min:1',
            'is_required' => 'boolean',
            'time_limit' => 'nullable|integer|min:1',
        ]);
        
        // Фильтруем пустые опции
        if (isset($validated['options']) && is_array($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], function($option) {
                return !empty($option);
            });
        }
        
        // Добавляем параметры шкалы, если тип вопроса - шкала
        if ($validated['type'] === 'scale') {
            if (!isset($validated['options'])) {
                $validated['options'] = [];
            }
            $validated['options']['min'] = $request->input('scale_min', 1);
            $validated['options']['max'] = $request->input('scale_max', 10);
        }
        
        $question->update($validated);
        
        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Вопрос успешно обновлен!');
    }

    /**
     * Удаление вопроса
     */
    public function destroy(Survey $survey, Question $question)
    {
        Gate::authorize('update', $survey);
        
        if ($question->survey_id !== $survey->id) {
            abort(404);
        }
        
        $question->delete();
        
        // Обновляем позиции оставшихся вопросов
        $survey->questions()->where('position', '>', $question->position)
            ->decrement('position');
        
        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Вопрос успешно удален!');
    }
    
    /**
     * Изменение порядка вопросов
     */
    public function reorder(Request $request, Survey $survey)
    {
        Gate::authorize('update', $survey);
        
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'integer|exists:questions,id',
        ]);
        
        $position = 1;
        foreach ($validated['questions'] as $questionId) {
            $question = Question::find($questionId);
            if ($question && $question->survey_id === $survey->id) {
                $question->update(['position' => $position]);
                $position++;
            }
        }
        
        return response()->json(['success' => true]);
    }
}
