<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Role;
use App\Models\ScheduleSetting;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    //
    public function index(Request $request)
    {
        $data['columns'] = $columns = collect([
            ['key' => 'sr', 'label' => 'Sr.', 'sortable' => false, 'searchable' => false],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'searchable' => true, 'show' => true],
            ['key' => 'content', 'label' => 'Content', 'sortable' => false, 'searchable' => true, 'show' => true],
            ['key' => 'file_image', 'label' => 'Image', 'sortable' => false, 'searchable' => false, 'show' => true],
            ['key' => 'created_at', 'label' => 'Created At', 'sortable' => true, 'searchable' => false, 'show' => true],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'searchable' => false, 'show' => true],
        ]);

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('title', 'LIKE', "%{$value}%")
                      ->orWhere('content', 'LIKE', "%{$value}%");
            });
        });

        $data['items'] = QueryBuilder::for(News::class)
            ->allowedSorts($columns->where('sortable', true)->pluck('key')->toArray())
            ->allowedFilters([$globalSearch])
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

            return inertia('Admin/News/Index', [
                'data' => $data,
                'imagePath' => asset('uploads/news/'),
            ]);
    }

    public function store(Request $request)
{
    try {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Add image validation
        ]);
    
        $news = new News();
        $this->saveData($news, $request);
    
        return redirect()->route('news.index')
            ->with('success', 'News added successfully'); // Update message
    } catch (\Exception $e) {
        // Handle the exception and return an error message
        return redirect()->route('news.index')
            ->with('error', 'An error occurred while adding the news: ' . $e->getMessage());
    }
}


    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'file_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Add image validation
            ]);
    
            $news = News::findOrFail($id);
            $this->saveData($news, $request);
    
            return redirect()->route('news.index')
                ->with('success', 'News updated successfully');
        } catch (\Exception $e) {
            // Handle the exception and return an error message
            return redirect()->route('news.index')
                ->with('error', 'An error occurred while updating the news: ' . $e->getMessage());
        }
    }
    
    
    protected function saveData($news, $request)
    {
        $file = $request->file('file_image');
        $news->title = $request['title'];
        $news->slug = Str::slug($request['title']);
        $news->content = $request['content'];

        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/news'), $fileName);
            $news->file_image = $fileName;
        }

        $news->save();

        return $news;
    }

    public function destroy($id)
    {
        try {
            $news = News::findOrFail($id);
            $filePath = public_path('uploads/news/' . $news->file_image);
    
            if (file_exists($filePath)) {
                unlink($filePath);
            }
    
            $news->delete();
    
            return redirect()->route('news.index')
                ->with('success', 'News deleted successfully');
        } catch (\Exception $e) {
            // Handle the exception and return an error message
            return redirect()->route('news.index')
                ->with('error', 'An error occurred while deleting the news: ' . $e->getMessage());
        }
    }
    
}
