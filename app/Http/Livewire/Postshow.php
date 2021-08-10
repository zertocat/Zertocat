<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;

class Postshow extends Component
{
    public $post;
    public function mount($slug)
    {
        $this->post = Post::where('slug', $slug)->first();
    }
    public function render()
    {
        return view('livewire.postshow')->layout('layouts.guest');
    }
}
