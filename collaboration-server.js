import { Server } from '@hocuspocus/server'

const server = new Server({
  port: 1234,
})

server.listen()

console.log('Hocuspocus server running on ws://127.0.0.1:1234')