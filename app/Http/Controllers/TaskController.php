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
        $users = User::all();
        return Inertia::render('Task/Index', [
            'tasks' => $tasks,
            'users' => $users,
        ]);
    }

    public function userTask(User $user)
    {
        if ($user->id !== Auth::id()) {
            throw new Exception("You dont have the permission to access these tasks");
        }

        $tasks = Task::with(['user'])->where('user_id', $user->id)->get();
        return Inertia::render('MyTask/Index', [
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'deadline' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'user_id' => 'required|exists:users,id'
        ]);

        $task = Task::create($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task assigned successfully.');
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'deadline' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $data['user_id'] = Auth::id();

        $task = Task::create($data);

        return redirect()->route('tasks.userTask', [ 'user' => Auth::id()])
            ->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:pending,ongoing,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high',
            'user_id' => 'nullable|exists:users,id', 
        ]);

        $task->update($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function userUpdate(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            throw new Exception("You dont have the permission to update this task");
        }
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:pending,ongoing,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $task->update($data);

        return redirect()->route('tasks.userTask', ['user' => Auth::id()])
            ->with('success', 'Task updated successfully.');
    }
}
