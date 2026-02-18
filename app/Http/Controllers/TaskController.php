<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['user', 'attachments'])->get();
        $tasks->each(function ($task) {
            $task->attachments->each(function ($att) {
                $att->filepath = Storage::url($att->filepath);
            });
        });
        return Inertia::render('Task/Index', [
            'tasks' => $tasks,
            'users' => User::all()
        ]);
    }

    public function userTask(User $user)
    {
        if ($user->id !== Auth::id()) {
            throw new Exception("You dont have the permission to access these tasks");
        }

        $tasks = Task::with(['user', 'attachments'])->where('user_id', $user->id)->get();
        return Inertia::render('MyTask/Index', [
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|nullable', 
            'deadline' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'user_id' => 'required|exists:users,id',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120'
        ]);

        $task = Task::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $task->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $path,
                    'mimetype' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task assigned successfully.');
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'deadline' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120'
        ]);

        $data['user_id'] = Auth::id();

        $task = Task::create($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $task->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $path,
                    'mimetype' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

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
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
            'deleted_attachments' => 'nullable|array',
            'deleted_attachments.*' => 'exists:attachments,id'
        ]);

        try {
            DB::transaction(function () use ($request, $task, $data) {

            $task->update($data);

            if (!empty($request->deleted_attachments)) {
                $task->attachments()
                    ->whereIn('id', $request->deleted_attachments)
                    ->each(function ($attachment) {
                        Storage::disk('public')->delete($attachment->filepath);
                        $attachment->delete();
                    });
            }

            // Handle new uploaded attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    $task->attachments()->create([
                        'filename' => $file->getClientOriginalName(),
                        'filepath' => $path,
                        'mimetype' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }
        });

        } catch (\Throwable $th) {
            Log::error($th);
        }

        
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
