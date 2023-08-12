const express = require('express');
const http = require('http');
const socketIO = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = socketIO(server);

// Serve static files (your HTML, CSS, and JS)
app.use(express.static(__dirname + '/public'));

app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html'); // Adjust the file path if needed
  });

// Listen for incoming WebSocket connections
io.on('connection', (socket) => {
  console.log('A user connected');

  socket.on("updateStatus", (data) => {
    io.emit("updatedStatus", {
        index: data.index,
        newStatus: data.newStatus,
        followUpValue: data.followUpValue
     });
    });
    
    socket.on("updateFollowUp", function (data) {
        io.emit("updatedFollowUp", data); // Broadcast to all clients
    });


  // Handle messages from clients
    socket.on("returnData", (data) => {
        io.emit("returnData", data);
    });
  
    socket.on("deleteRow", (index) => {
        // Delete the row with the corresponding index
        io.emit("deleteRow", index);
    });

    socket.on("refreshPage", () => {
        // Refresh the page to synchronize the changes
        io.emit("refreshPage");
    });

    // Handle disconnect
    socket.on('disconnect', () => {
        console.log('A user disconnected');
    });

});
// Start the server
const port = process.env.PORT || 3000;
server.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
