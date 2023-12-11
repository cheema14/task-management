@extends('layouts.admin')
@section('content')
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


    <div id="task-container">
        <ul id="sortable" class="sortable-grid">    
            @foreach ($tasks as $task)
                <li class="ui-state-default" data-id="{{ $task->id }}" data-priority="{{ $task->priority }}">{{ $task->name }}</li>
            @endforeach
        </ul>    
    </div>

@endsection
@section('scripts')
@parent

<script>
    

    $( function() {
        $( "#sortable" ).sortable(
        {
            grid: [ 2, 1 ],
            stop: function(event, ui) {
                
                const currentOrder = $("#sortable").sortable("toArray", { attribute: "data-priority" });
                
                const currentOrderObject = currentOrder.reduce((acc, taskId, index) => {
                    
                    acc[index+1] = taskId; // Assuming 1-based priority (adjust as needed)
                    return acc;
                }, {});

                console.log("currentOrder",currentOrder);
                console.log("The object",currentOrderObject);


                $.ajax({
                    type:'POST',
                    url:'{{ route('admin.tasks.updatePriority') }}',
                    headers: {
                            'X-CSRF-TOKEN': '<?php echo csrf_token() ?>'
                        },
                    data:{ tasksOrder:currentOrder},
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