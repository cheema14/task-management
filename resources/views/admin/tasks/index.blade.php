@extends('layouts.admin')
@section('content')
@can('task_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.tasks.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.task.title_singular') }}
            </a>
        </div>

        
        
    </div>
@endcan
@section('styles')

<style>
    #task-container {
      display: flex;
      flex-direction: column;
      width: 300px;
    }

    .task {
      border: 1px solid #ccc;
      padding: 10px;
      margin: 5px;
      cursor: grab;
      user-select: none;
    }
    .sortable-grid {
        list-style-type: none;
        margin: 0;
        padding: 0;
    } 

    .sortable-grid li {
        margin: 5px;
        padding: 10px;
        background-color: #eee;
        cursor: grab;
    }
  </style>

@endsection
<div class="card">
    <div class="card-header">
        {{ trans('cruds.task.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.tasks.filter") }}" enctype="multipart/form-data">
            @csrf
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="required" for="project_id">{{ trans('cruds.project.fields.project') }}</label>
                    <select class="form-control select2 {{ $errors->has('project') ? 'is-invalid' : '' }}" name="project_id" id="project_id" required>
                        @foreach($projects as $id => $entry)
                            <option value="{{ $id }}" {{ request()->project_id == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('project'))
                        <span class="text-danger">{{ $errors->first('project') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.project.fields.project_helper') }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-info" type="submit">
                    {{ trans('cruds.task.fields.filter_tasks') }}
                </button>
            </div>
        </form>
        <div id="task-container">
            <ul id="sortable" class="sortable-grid">    
                @foreach ($tasks as $task)
                    <li class="ui-state-default" data-id="{{ $task->id }}" data-priority="{{ $task->priority }}">
                        {{ $task->name }}
                        @can('task_edit')
                            <a style="float:right;margin-top:3px;margin-left:3px;" class="btn btn-xs btn-primary" href="{{ route('admin.tasks.edit', $task->id) }}">
                                {{ trans('global.edit') }}
                            </a>    
                        @endcan
                        
                        @can('task_delete')
                            <form style="float:right" action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                            </form>
                        @endcan
                    </li>
                @endforeach
            </ul>    
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    disable = false;
    @if (request()->project_id)
            disable = true;
    @endif

    $( function() {
        $( "#sortable" ).sortable(
        {
            disabled:disable,
            stop: function(event, ui) {
                
                const currentIds = $("#sortable").sortable("toArray", { attribute: "data-id"  });
                
                $.ajax({
                    type:'POST',
                    url:'{{ route('admin.tasks.updatePriority') }}',
                    headers: {
                            'X-CSRF-TOKEN': '<?php echo csrf_token() ?>'
                        },
                    data:{ taskId:currentIds},
                    success:function(data) {
                        // if(data){
                        // }
                        // else{
                        // }
                    }
                });
            
                
            },
        },
        
        );
    $( "#sortable" ).disableSelection();
    });
</script>
@endsection