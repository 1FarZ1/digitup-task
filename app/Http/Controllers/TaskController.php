<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->user()->role === 'admin') {
                $tasks = Task::withTrashed()->get();
            } else {
                $tasks = Task::where('user_id', $request->user()->id)
                ->whereNull('deleted_at')
                ->get();
            }

            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching tasks.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $task = Task::withTrashed()->findOrFail($id);

            if ($request->user()->role == 'admin' || $task->user_id == $request->user()->id) {
                return $task;
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching the task.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(TaskCreateRequest $request)
    {
        try {
           $request->validated();

            $due_date = Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d');
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $due_date,
                'user_id' => $request->user()->id,
            ]);

            return response()->json($task, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the task.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(TaskUpdateRequest $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if ($request->user()->role != 'admin' && $task->user_id != $request->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            $request->validated();



            $task->update(
                $request->all()
            );


            return response()->json($task, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the task.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $task = Task::withTrashed()->findOrFail($id);

            if ($request->user()->role != 'admin' && $task->user_id != $request->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            if ($task->trashed()) {
                return response()->json(['message' => 'Task already deleted'], 400);
            }
            $task->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the task.', 'error' => $e->getMessage()], 500);
        }
    }

    public function deletedTasks(Request $request)
    {
        try {
            if ($request->user()->role != 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return Task::onlyTrashed()->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching deleted tasks.', 'error' => $e->getMessage()], 500);
        }
    }
}
