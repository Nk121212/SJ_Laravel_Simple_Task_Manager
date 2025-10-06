<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        ul { list-style: none; padding: 0; }
        li { padding: 8px; margin: 4px 0; background: #f0f0f0; cursor: grab; }
    </style>
</head>
<body>
    <h1>Task Manager</h1>

    <form method="GET" action="{{ route('tasks.index') }}">
        <select name="project_id" onchange="this.form.submit()">
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
    </form>

    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <input type="hidden" name="project_id" value="{{ $projectId }}">
        <input type="text" name="name" placeholder="Task name" required>
        <button type="submit">Add Task</button>
    </form>

    <ul id="task-list">
        @foreach($tasks as $task)
            <li data-id="{{ $task->id }}">
                <form method="POST" action="{{ route('tasks.update', $task) }}">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $task->name }}">
                    <button type="submit">Update</button>
                </form>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                    @csrf @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let list = document.getElementById('task-list');
        new Sortable(list, {
            animation: 150,
            onEnd: function () {
                let ids = [];
                document.querySelectorAll('#task-list li').forEach(li => ids.push(li.dataset.id));

                fetch("{{ route('tasks.reorder') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({ ids })
                });
            }
        });
    </script>
</body>
</html>
