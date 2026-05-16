<x-app-layout>
            Collaborative editing active
        </div>

        <div id="editor" style="height:400px;">
            {!! $document->content !!}
        </div>

        <div class="mt-4">
            <a href="/documents/{{ $document->id }}/revisions"
               class="bg-blue-500 text-white px-4 py-2 rounded">
               Version History
            </a>
        </div>

    </div>

</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>

const quill = new Quill('#editor', {
    theme: 'snow'
});

let timeout = null;

quill.on('text-change', function () {

    clearTimeout(timeout);

    timeout = setTimeout(() => {

        fetch('/documents/{{ $document->id }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                content: quill.root.innerHTML
            })
        });

    }, 500);
});

window.Echo.channel('document.{{ $document->id }}')
.listen('.document.updated', (e) => {

    quill.root.innerHTML = e.document.content;

    console.log('Updated by: ' + e.userName);
});

</script>

</x-app-layout>