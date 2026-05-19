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
const typingIndicatorEl = document.querySelector('#typingIndicator')
const onlineUsersEl = document.querySelector('#onlineUsers')
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

  provider.awareness.setLocalStateField('user', {
    name: editorName,
    color: stringToColor(editorName),
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

  const renderOnlineUsers = () => {
    if (!onlineUsersEl) return

    const states = Array.from(provider.awareness.getStates().values())
    const users = states
      .map((state) => state?.user)
      .filter((user) => user?.name)

    const unique = []
    const seen = new Set()

    for (const user of users) {
      if (seen.has(user.name)) continue
      seen.add(user.name)
      unique.push(user)
    }

    onlineUsersEl.innerHTML = unique.map((user) => {
      const color = user.color || '#2563eb'
      const name = user.name || 'Anonymous'
      const initial = name.charAt(0).toUpperCase()

      return `
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-slate-50 text-sm text-slate-700">
          <span class="w-2.5 h-2.5 rounded-full" style="background:${color}"></span>
          <span class="flex h-6 w-6 items-center justify-center rounded-full text-white text-xs font-bold" style="background:${color}">
            ${initial}
          </span>
          <span>${name}</span>
        </span>
      `
    }).join('')
  }

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
      const revisionContent = encodedContent ? atob(encodedContent) : '<p></p>'

      if (documentTitleEl) {
        documentTitleEl.textContent = revisionTitle
      }

      editor.commands.setContent(revisionContent, false)
    })
  })

  provider.awareness.on('change', () => {
    renderOnlineUsers()
  })

  renderOnlineUsers()

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
