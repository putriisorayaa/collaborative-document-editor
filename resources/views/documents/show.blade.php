<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-6">
        <div class="max-w-7xl mx-auto px-4 space-y-6">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">

                <div class="p-6 border-b border-slate-200">

                    <div class="flex items-center justify-between gap-4 flex-wrap">

                        <div>
                            <h1 id="documentTitleText" class="text-3xl font-bold text-slate-900">
                                {{ $document->title }}
                            </h1>

                            <p class="text-sm text-slate-500 mt-1">
                                Real-time collaborative editor
                            </p>

                            <p id="typingIndicator" class="text-sm text-blue-500 mt-1"></p>

                            <div class="mt-3">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                    Online
                                </span>

                                <div id="onlineUsers" class="flex flex-wrap gap-2 mt-2"></div>
                            </div>
                        </div>

                        <button
                            id="saveBtn"
                            type="button"
                            class="px-4 py-2 rounded-xl bg-black text-white hover:opacity-90 transition"
                        >
                            Save
                        </button>

                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-2 border-t pt-4">

                        <button class="toolbar-btn" data-action="bold">
                            Bold
                        </button>

                        <button class="toolbar-btn" data-action="italic">
                            Italic
                        </button>

                        <button class="toolbar-btn" data-action="underline">
                            Underline
                        </button>

                        <button class="toolbar-btn" data-action="h1">
                            H1
                        </button>

                        <button class="toolbar-btn" data-action="h2">
                            H2
                        </button>

                        <button class="toolbar-btn" data-action="bullet">
                            Bullet
                        </button>

                        <button class="toolbar-btn" data-action="ordered">
                            Ordered
                        </button>

                        <button class="toolbar-btn" data-action="undo">
                            Undo
                        </button>

                        <button class="toolbar-btn" data-action="redo">
                            Redo
                        </button>

                    </div>
                </div>

                <div class="p-8">
                    <div
                        id="editor"
                        data-document-id="{{ $document->id }}"
                        class="tiptap min-h-[700px] outline-none"
                    ></div>
                </div>

            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-5">

                <div class="flex items-center justify-between mb-4">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Version History
                    </h2>

                    <span class="text-sm text-slate-500">
                        {{ $document->revisions->count() }} revisions
                    </span>

                </div>

                <div class="space-y-3">

                    @forelse($document->revisions as $revision)

                        <button
                            type="button"
                            class="restore-btn w-full text-left p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition"
                            data-title="{{ $revision->title }}"
                            data-content="{{ base64_encode($revision->content) }}"
                        >

                            <p class="font-medium text-slate-900">
                                Revision #{{ $revision->revision_no }}
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                By {{ $revision->editor_name ?? '-' }}
                                • {{ $revision->created_at->diffForHumans() }}
                            </p>

                        </button>

                    @empty

                        <p class="text-sm text-slate-500">
                            No revisions yet
                        </p>

                    @endforelse

                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Edit Tracking
                    </h2>
                </div>

                <div class="space-y-3">
                    @forelse($document->editLogs as $log)
                        <details class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <summary class="cursor-pointer list-none">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-slate-900">
                                            {{ $log->editor_name }}
                                        </p>
                                        <p class="text-sm text-slate-500 mt-1">
                                            {{ $log->summary ?? 'Updated document' }}
                                        </p>
                                    </div>

                                    <span class="text-xs text-slate-500">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </summary>

                            <div class="mt-4 space-y-3 text-sm">
                                <div>
                                    <p class="font-semibold text-slate-700 mb-1">Before</p>
                                    <div class="rounded-lg bg-white border border-slate-200 p-3 overflow-x-auto">
                                        <pre class="whitespace-pre-wrap text-slate-700 m-0">{{ strip_tags($log->content_before) }}</pre>
                                    </div>
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-700 mb-1">After</p>
                                    <div class="rounded-lg bg-white border border-slate-200 p-3 overflow-x-auto">
                                        <pre class="whitespace-pre-wrap text-slate-700 m-0">{{ strip_tags($log->content_after) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </details>
                    @empty
                        <p class="text-sm text-slate-500">
                            Belum ada log perubahan.
                        </p>
                    @endforelse
                </div>
            </div>
            <script>
                window.initialDocumentContent = @json($initialContent ?? $document->content);
                window.authUserName = @json(auth()->user()->name);
            </script>
        </div>
    </div>
</x-app-layout>