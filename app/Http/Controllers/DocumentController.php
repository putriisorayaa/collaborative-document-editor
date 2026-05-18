<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->get();

        return view('dashboard', compact('documents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $document = Document::create([
            'title' => $data['title'],
            'content' => '<p></p>',
            'last_edited_at' => now(),
            'last_editor_name' => null,
        ]);

        return redirect()->route('documents.show', $document);
    }

    public function show(Document $document)
    {
        $document->load('revisions');

        $initialContent = $document->content;

        if (blank(trim(strip_tags($initialContent ?? '')))) {
            $initialContent = $document->revisions()
                ->latest('revision_no')
                ->value('content') ?? '<p></p>';
        }

        return view('documents.show', compact('document', 'initialContent'));
    }

    public function save(Request $request, Document $document)
    {
        $data = $request->validate([
            'content' => ['required', 'string'],
            'editor_name' => ['nullable', 'string', 'max:100'],
        ]);

        $editorName = $data['editor_name'] ?? 'Anonymous';

        $document->update([
            'content' => $data['content'],
            'last_edited_at' => now(),
            'last_editor_name' => $editorName,
        ]);

        $revisionNo = (int) ($document->revisions()->max('revision_no') ?? 0) + 1;

        $document->revisions()->create([
            'revision_no' => $revisionNo,
            'title' => $document->title,
            'editor_name' => $editorName,
            'content' => $data['content'],
            'summary' => 'Save',
        ]);

        return response()->json([
            'message' => 'saved',
        ]);
    }

    public function destroy(Document $document)
    {
        $document->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Document deleted.');
    }
}