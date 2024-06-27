<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role == 'admin') {
            return Task::withTrashed()->get();
        }

        return Task::where('user_id', $request->user()->id)->orWhereNull('deleted_at')->get();
    }

    public function show(Request $request, $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        if ($request->user()->role == 'admin' || $task->user_id == $request->user()->id) {
            return $task;
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,completed',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        if ($request->user()->role != 'admin' && $task->user_id != $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,completed',
        ]);

        $task->update($request->all());

        return response()->json($task, 200);
    }

    public function destroy(Request $request, $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        if ($request->user()->role != 'admin' && $task->user_id != $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(null, 204);
    }

    public function deletedTasks(Request $request)
    {
        if ($request->user()->role != 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return Task::onlyTrashed()->get();
    }
}
