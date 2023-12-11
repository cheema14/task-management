<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = Task::orderBy('priority', 'ASC');

        if ($request->has('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        $tasks = $query->get();

        // $tasks = Task::orderBy('priority', 'ASC')->get();

        $projects = Project::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.tasks.index', compact('tasks', 'projects'));
    }

    public function create()
    {
        abort_if(Gate::denies('task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.tasks.create', compact('projects'));
    }

    public function store(StoreTaskRequest $request)
    {
        if (! empty($request->project_id)) {

            $project = Project::find($request->project_id);

            $task = $project->tasks()->create([
                'name' => $request->name,
                'priority' => $request->priority,
            ]);

            return redirect()->route('admin.tasks.index');
        }

    }

    public function edit(Task $task)
    {
        abort_if(Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->all());

        return redirect()->route('admin.tasks.index');
    }

    public function show(Task $task)
    {
        abort_if(Gate::denies('task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.tasks.show', compact('task'));
    }

    public function destroy(Task $task)
    {
        abort_if(Gate::denies('task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaskRequest $request)
    {
        $tasks = Task::find(request('ids'));

        foreach ($tasks as $task) {
            $task->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function update_task_priority(Request $request)
    {

        $taskIds = $request->input('taskId');

        $setPriority = 1;
        foreach ($taskIds as $task) {
            $task = Task::where('id', $task)->first();
            $task->priority = $setPriority;
            $task->save();
            $setPriority++;

        }

        return response('successfully dragged', 200);

    }
}
