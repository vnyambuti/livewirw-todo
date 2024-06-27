<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;
class TodoList extends Component
{
  use WithPagination;
    #[Rule('required|min:3|max:50|unique:todos')]
    public $title;

    public $search;

    public $editingId;
    #[Rule('required|min:3|max:50')]
    public $editingTitle;

    function create()  {
     $validated= $this->validateOnly('title');

         Todo::create($validated);

         $this->reset('title');

          session()->flash('success','Saved');
          $this->resetPage();
    }
    function delete($id)  {
        try {
            Todo::findOrfail($id)->delete();
        } catch (\Exception $th) {
            
           session()->flash('error',$th->getMessage());
        //    dd(session()->all());
           return;
        }
       
    }

    function edit($id)  {
        $this->editingId=$id;
        $this->editingTitle=Todo::find($id)->title;
        
    }

    function cancel()  {
        $this->reset('editingId','editingTitle');
    }

    function update() {
       
        $validated= $this->validateOnly('editingTitle');
        $todo=Todo::find($this->editingId);
        $todo->title=$this->editingTitle;
        $todo->save();
        $this->cancel();
    }


    function toogleComplete($id) {
        
        $todo=Todo::find($id);
        $todo->status=!$todo->status;
        $todo->save();
    }
    public function render()
    {
        return view('livewire.todo-list',[
            'todos'=>Todo::latest()->where('title', 'like', '%'. $this->search .'%')->paginate(5)
        ]);
    }
}
