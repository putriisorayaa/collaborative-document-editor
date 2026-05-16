<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRevision;
use Illuminate\Http\Request;
use App\Events\DocumentUpdated;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->get();

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        Document::create([
            'title' => $request->title,
            'content' => '',
            'user_id' => auth()->id()
        ]);

        return redirect('/documents');
    }

    public function edit(Document $document)
    {
        return view('documents.editor', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $document->update([
            'content' => $request->content
        ]);

        DocumentRevision::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        broadcast(new DocumentUpdated(
            $document,
            auth()->user()->name
        ))->toOthers();

        return response()->json([
            'success' => true
        ]);
    }

    public function revisions(Document $document)
    {
        $revisions = $document->revisions()
            ->latest()
            ->with('user')
            ->get();

        return view('documents.revisions', compact(
            'document',
            'revisions'
        ));
    }
}