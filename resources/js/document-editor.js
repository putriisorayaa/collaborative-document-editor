import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import Collaboration from '@tiptap/extension-collaboration'
import CollaborationCaret from '@tiptap/extension-collaboration-caret'
import Underline from '@tiptap/extension-underline'
import * as Y from 'yjs'
import { HocuspocusProvider } from '@hocuspocus/provider'

const editorEl = document.querySelector('#editor')
const saveBtn = document.querySelector('#saveBtn')
const documentId = editorEl?.dataset?.documentId
const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
const documentTitleEl = document.querySelector('#documentTitleText')
const editorName = window.authUserName || 'Anonymous'

function stringToColor(str) {
  let hash = 0

  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash)
  }

  let color = '#'

  for (let i = 0; i < 3; i++) {
    const value = (hash >> (i * 8)) & 255
    color += (`00${value.toString(16)}`).slice(-2)
  }

  return color
}

if (editorEl && documentId) {
  const ydoc = new Y.Doc()

  const provider = new HocuspocusProvider({
    url: 'ws://127.0.0.1:1234',
    name: `document-${documentId}`,
    document: ydoc,
  })

  const editor = new Editor({
    element: editorEl,
    extensions: [
      StarterKit.configure({ history: false }),
      Underline,
      Placeholder.configure({ placeholder: 'Start typing...' }),
      Collaboration.configure({ document: ydoc }),
      CollaborationCaret.configure({
        provider,
        user: {
          name: editorName,
          color: stringToColor(editorName),
        },
      }),
    ],
    content: window.initialDocumentContent || '<p></p>',
  })

  document.querySelectorAll('.toolbar-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const action = btn.dataset.action

      if (action === 'bold') editor.chain().focus().toggleBold().run()
      if (action === 'italic') editor.chain().focus().toggleItalic().run()
      if (action === 'underline') editor.chain().focus().toggleUnderline().run()
      if (action === 'h1') editor.chain().focus().toggleHeading({ level: 1 }).run()
      if (action === 'h2') editor.chain().focus().toggleHeading({ level: 2 }).run()
      if (action === 'bullet') editor.chain().focus().toggleBulletList().run()
      if (action === 'ordered') editor.chain().focus().toggleOrderedList().run()
      if (action === 'undo') editor.chain().focus().undo().run()
      if (action === 'redo') editor.chain().focus().redo().run()
    })
  })

  document.querySelectorAll('.restore-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const revisionTitle = btn.dataset.title || 'Untitled Document'
      const encodedContent = btn.dataset.content || ''
      const revisionContent = encodedContent
        ? atob(encodedContent)
        : '<p></p>'

      if (documentTitleEl) {
        documentTitleEl.textContent = revisionTitle
      }

      editor.commands.setContent(revisionContent, false)
    })
  })

  saveBtn?.addEventListener('click', async () => {
    const currentContent = editor.getHTML()

    const response = await fetch(`/documents/${documentId}/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        content: currentContent,
        editor_name: editorName,
      }),
    })

    if (response.ok) {
      window.location.href = '/dashboard'
      return
    }

    console.error(await response.text())
    alert('Save failed')
  })

  window.addEventListener('beforeunload', () => {
    provider.destroy()
    editor.destroy()
  })
}