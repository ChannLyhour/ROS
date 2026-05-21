<?php

namespace App\Http\Controllers\admin;

use App\Models\Translation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TranslationController extends Controller
{
    /**
     * Display a listing of translations.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Translation::class);
        $query = Translation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('en', 'like', "%{$search}%")
                  ->orWhere('kh', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%");
            });
        }

        $translations = $query->orderBy('group')
            ->orderBy('key')
            ->paginate(10)
            ->withQueryString();

        return view('admin.translations.index', compact('translations'));
    }

    /**
     * Show the form for creating a new translation.
     */
    public function create()
    {
        $this->authorize('create', Translation::class);
        return view('admin.translations.create');
    }

    /**
     * Store a newly created translation in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Translation::class);
        $request->validate([
            'key' => 'required|unique:translations,key',
            'en' => 'nullable',
            'kh' => 'nullable',
            'group' => 'required',
        ]);

        Translation::create($request->all());

        return redirect()->route('translations.index')->with('success', 'Translation created successfully');
    }

    /**
     * Show the form for editing the specified translation.
     */
    public function edit(Translation $translation)
    {
        $this->authorize('update', $translation);
        return view('admin.translations.edit', compact('translation'));
    }

    /**
     * Update the specified translation in storage.
     */
    public function update(Request $request, Translation $translation)
    {
        $this->authorize('update', $translation);
        $request->validate([
            'key' => 'required|unique:translations,key,' . $translation->id,
            'en' => 'nullable',
            'kh' => 'nullable',
            'group' => 'required',
        ]);

        $translation->update($request->all());

        return redirect()->route('translations.index')->with('success', 'Translation updated successfully');
    }

    /**
     * Remove the specified translation from storage.
     */
    public function destroy(Translation $translation)
    {
        $this->authorize('delete', $translation);
        $translation->delete();
        return redirect()->route('translations.index')->with('success', 'Translation deleted successfully');
    }
}
