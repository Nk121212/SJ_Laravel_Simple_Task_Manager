<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::all();
        $projectId = $request->get('project_id', $projects->first()?->id);

        $tasks = Task::where('project_id', $projectId)->orderBy('priority')->get();

        return view('tasks.index', compact('tasks', 'projects', 'projectId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'project_id' => 'required|exists:projects,id'
        ]);

        $maxPriority = Task::where('project_id', $data['project_id'])->max('priority') ?? 0;
        $data['priority'] = $maxPriority + 1;

        Task::create($data);

        return redirect()->back();
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->only('name'));
        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back();
    }

    public function reorder(Request $request)
    {
        $ids = $request->input('ids'); // array of task IDs in new order

        foreach ($ids as $index => $id) {
            Task::where('id', $id)->update(['priority' => $index + 1]);
        }

        return response()->json(['status' => 'success']);
    }
}
