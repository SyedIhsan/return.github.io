const express = require('express');
const http = require('http');
const socketIO = require('socket.io');
const path = require('path');

const app = express();
const server = http.createServer(app);
const io = socketIO(server);

// Your existing server configuration and routes
// Add any middleware, routes, and static file serving as needed
app.use(express.static(path.join(__dirname, 'return.github.io')));

app.get('/', (req, res) => {
  res.sendFile(__dirname + '/return.github.io/index.html');
});

// Listen for incoming WebSocket connections
io.on('connection', (socket) => {
  console.log('A user connected');

  // Handle updates from clients
  socket.on('newReturn', (returnData) => {
    // Broadcast the new return data to all connected clients
    io.emit('returnData', returnData);
  });

  // Handle delete events from clients
  socket.on('deleteReturn', (index) => {
    // Broadcast the delete event to all connected clients
    io.emit('deleteReturn', index);
  });

  // Handle disconnect
  socket.on('disconnect', () => {
    console.log('A user disconnected');
  });
});

// Start the server
const port = process.env.PORT || 4000;
server.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
