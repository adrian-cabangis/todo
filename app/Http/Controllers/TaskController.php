<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['user'])->get();
        return Inertia::render('Task/Index', [
            'tasks' => $tasks
        ]);
    }

    public function userTask(User $user)
    {
        if ($user->id !== Auth::id()) {
            throw new Exception("You dont have the rights to access others tasks");
        }

        $tasks = Task::with(['user'])->where('user_id', $user->id)->get();
        return Inertia::render('Task/Index', [
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'deadline' => 'nullable|date',
            'priority' => 'enum|nullable|in:low,medium,high',
        ]);

        $task = Task::create($data);

        return redirect()->route('tasks.index')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:pending,ongoing,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        

        return redirect()->route('tasks.index')
            ->with('success', 'User created successfully.');
    }
}
