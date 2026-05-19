<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="max-w-6xl mx-auto px-4">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">
                        Collaborative Documents
                    </h1>
                    <p class="text-slate-500 mt-1">
                        Create and open your documents
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-8">
                <form action="{{ route('documents.store') }}" method="POST" class="flex flex-col md:flex-row gap-3">
                    @csrf

                    <input
                        type="text"
                        name="title"
                        placeholder="Document title"
                        class="flex-1 rounded-xl border-slate-300 px-4 py-3 focus:border-black focus:ring-black"
                        required
                    >

                    <button
                        type="submit"
                        class="px-5 py-3 rounded-xl bg-black text-white hover:opacity-90 transition"
                    >
                        + New Document
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($documents as $document)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <h2 class="text-xl font-semibold text-slate-900 mb-6">
                            {{ $document->title }}
                        </h2>

                        <div class="flex items-center justify-between">
                            <a
                                href="{{ route('documents.show', $document) }}"
                                class="text-sm font-medium text-black hover:underline"
                            >
                                Open
                            </a>

                            <form
                                action="{{ route('documents.destroy', $document) }}"
                                method="POST"
                                onsubmit="return confirm('Delete this document?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-sm font-medium text-red-500 hover:text-red-700"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm border border-slate-200 p-10 text-center">
                        <p class="text-slate-500">
                            Belum ada dokumen
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>