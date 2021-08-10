<?php

namespace App\Http\Livewire;

use App\Models\Post;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class Posts extends Component
{

    use WithFileUploads;
    use WithPagination;
    public $title;
    public $body;
    public $image;
    public $newImage;
    public $postId = null;
    public $showModalForm = false;

    public function showCreatePostModal()
    {
        $this->showModalForm = true;
    }
    public function updateShowForm()
    {
        $this->reset();
    }
    public function storePost()
    {
        $this->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,gif',
        ]);



        $image_name = $this->image->getClientOriginalName();
        $this->image->storeAs('public/photos/', $image_name);

        $post = new Post();
        $post->user_id = auth()->user()->id;
        $post->title = $this->title;
        $post->slug = str::slug($this->title);
        $post->body = $this->body;
        $post->image = $image_name;
        $post->save();
        $this->reset();
        session()->flash('flash.banner', 'Post created Successfully!');
    }
    public function showEditPost($id)
    {
        $this->showModalForm = true;
        $this->postId = $id;
        $this->loadEditeForm();
    }
    public function loadEditeForm()
    {
        $post = Post::findOrFail($this->postId);
        $this->title = $post->title;
        $this->body = $post->body;
        $this->newImage = $post->image;
    }
    public function updatePost()
    {
        $this->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,gif',
        ]);
        if ($this->image) {
            Storage::delete('public/photos/', $this->newImage);
            $image_name = $this->image->getClientOriginalName();
            $this->image->storeAs('public/photos/', $image_name);
        }
        Post::find($this->postId)->update([
            'title' => $this->title,
            'body'  => $this->body,
            'image' => $this->newImage
        ]);
        $this->reset();
        session()->flash('flash.banner', 'Post Updated Successfully!');
    }
    public function deletePost($id)
    {
        $post = Post::find($id);
        Storage::delete('public/photos/', $post->image);
        $post->delete();
        session()->flash('flash.banner', 'Post Deleted Successfully!');
    }
    public function render()
    {

        return view('livewire.posts', [
            'posts' => Post::orderBy('created_at', 'desc')->paginate(5)
        ]);
    }
}
