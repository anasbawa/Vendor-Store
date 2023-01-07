<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!Gate::allows('categories.view')) { // allows() != denies()
            abort(403); // or return view()
        }

        $request = request(); // Helper Function
        /**
         *  Filter Without Local Scope
         *  $query = Category::query();

         * if($name = $request->query('name')) {
         *    $query->where('name', 'LIKE', "%{$name}%");
         *}

         *  if ($status = $request->query('status')) {
         *    $query->whereStatus($status);
         *   }
         *  $categories = $query->paginate(2);
         */

        $categories = Category::with('parent')
            /* leftJoin('categories as parents', 'parents.id', '=', 'categories.parent_id')
            ->select([
                'categories.*',
                'parents.name as parent_name'
            ])
            ->select('categories.*')
            ->selectRaw("(SELECT COUNT(*) FROM products WHERE status = 'active' AND category_id = categories.id) as products_count") */
            ->withCount([
                'products as products_number' => function($query) {
                    $query->where('status', '=', 'active');
                }
            ])
            ->filter($request->query())
            ->orderBy('categories.name')
            ->paginate();



        return view('dashboard.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('categories.create')) { // allows() != denies()
            abort(403); // or return view()
        }

        $parents = Category::all();
        $category = new Category();
        return view('dashboard.categories.create', compact('parents', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('categories.create'); // throw an exception so we put it in try catch to handle it

        $request->validate(Category::rules());

         // Request Merge
         $request->merge([
            'slug' => Str::slug($request->post('name'))
        ]);

        $data = $request->except('image');

        $data['image'] = $this->uploadImage($request);

        // Mass Assignment
        $category = Category::create($data);
        // RPG
        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Category Created');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        if (!Gate::allows('categories.view')) { // allows() != denies()
            abort(403); // or return view()
        }
        return view('dashboard.categories.show', [
            'category' => $category
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Gate::authorize('categories.update');

        try {
            $category = Category::findOrFail($id);
        } catch (Exception $e) {
            return redirect()->route('dashboard.categories.index')
            ->with('info', 'Record Not Found');
        }


        $parents = Category::where('id', '<>', $id)
        ->where(function($query) use ($id) {
            $query->whereNull('parent_id')
                ->orWhere('parent_id', '<>', $id);
        })->get();

        return view('dashboard.categories.edit', compact('category','parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {

        $category = Category::findOrFail($id);

        $old_image = $category->image;

        $data = $request->except('image');

        $new_image = $this->uploadImage($request);

        if ($new_image) {
            $data['image'] = $new_image;
        }

        $category->update($data);

        if ($old_image && $new_image) {
            Storage::disk('public')->delete($old_image);
        }

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Category Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        Gate::authorize('categories.delete');
        $category->delete();

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Category Updated');

    }

    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('image')){
            return;
        }

            $file = $request->file('image'); // Uploaded File object
            $path = $file->store('uploads', [
                'disk' => 'public'
            ]);
            return $path;

    }

    public function trash()
    {
        $categories = Category::onlyTrashed()->paginate();
        return view('dashboard.categories.trash', compact('categories'));
    }

    public function restore(Request $request, $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        return redirect()->route('dashboard.categories.trash')
            ->with('success', 'Category restored!');
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        return redirect()->route('dashboard.categories.trash')
            ->with('success', 'Category deleted Forever');
    }
}
