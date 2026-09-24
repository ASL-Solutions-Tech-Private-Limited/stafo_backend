const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const mysql = require('mysql2/promise');
const path = require('path');
const fs = require('fs');

// Load environment variables from parent Laravel .env
const envPath = path.resolve(__dirname, '../.env');
if (fs.existsSync(envPath)) {
    require('dotenv').config({ path: envPath });
} else {
    require('dotenv').config();
}

const PORT = process.env.CHAT_PORT || 6001;

const app = express();
app.use(express.json());

// Basic health check endpoint
app.get('/health', (req, res) => {
    res.json({ status: 'ok', service: 'stafo-chat-server', timestamp: new Date() });
});

const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    },
    transports: ['websocket', 'polling'],
    pingTimeout: 30000,
    pingInterval: 25000
});

// MySQL Connection Pool
const pool = mysql.createPool({
    host: process.env.DB_HOST || '127.0.0.1',
    port: process.env.DB_PORT || 3306,
    user: process.env.DB_USERNAME || 'stafo_user',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_DATABASE || 'stafo_db',
    waitForConnections: true,
    connectionLimit: 15,
    queueLimit: 0
});

// Test DB Connection
pool.getConnection()
    .then(conn => {
        console.log(`[DB] Successfully connected to MySQL database: ${process.env.DB_DATABASE || 'stafo_db'}`);
        conn.release();
    })
    .catch(err => {
        console.error('[DB Error] Failed to connect to MySQL:', err.message);
    });

// In-memory tracking of active participants per room
// Format: roomName -> { adminCount: number, companyCount: number, sockets: Set }
const activeRooms = new Map();

io.on('connection', (socket) => {
    console.log(`[Socket Connected] ID: ${socket.id}`);

    let currentRoom = null;
    let userRole = null;
    let userCompanyId = null;

    // Join a specific chat room
    socket.on('join_room', async (data) => {
        const { company_id, role } = data || {};
        if (!company_id) return;

        const roomName = `company_${company_id}`;
        socket.join(roomName);
        currentRoom = roomName;
        userRole = role; // 'admin' or 'company'
        userCompanyId = company_id;

        if (!activeRooms.has(roomName)) {
            activeRooms.set(roomName, { adminCount: 0, companyCount: 0, sockets: new Set() });
        }

        const roomState = activeRooms.get(roomName);
        roomState.sockets.add(socket.id);
        if (role === 'admin') roomState.adminCount++;
        if (role === 'company') roomState.companyCount++;

        console.log(`[Join Room] Socket ${socket.id} joined ${roomName} as ${role}. Admins: ${roomState.adminCount}, Company: ${roomState.companyCount}`);

        // Broadcast presence to room
        io.to(roomName).emit('presence_update', {
            company_id: company_id,
            admin_online: roomState.adminCount > 0,
            company_online: roomState.companyCount > 0
        });

        // Auto mark seen if the opposite party is already active
        try {
            if (role === 'admin') {
                await pool.execute(
                    "UPDATE chats SET is_seen_admin = 1 WHERE company_id = ? AND message_by = 'company' AND (is_seen_admin IS NULL OR is_seen_admin = 0)",
                    [company_id]
                );
                io.to(roomName).emit('messages_seen', { company_id, seen_by: 'admin' });
            } else if (role === 'company') {
                await pool.execute(
                    "UPDATE chats SET is_seen_user = 1 WHERE company_id = ? AND message_by = 'admin' AND (is_seen_user IS NULL OR is_seen_user = 0)",
                    [company_id]
                );
                io.to(roomName).emit('messages_seen', { company_id, seen_by: 'company' });
            }
        } catch (err) {
            console.error('[Error marking seen on join]:', err.message);
        }
    });

    // Send Message
    socket.on('send_message', async (data, callback) => {
        try {
            const { company_id, message, message_by, sender, receiver, attachment } = data || {};
            if (!company_id || (!message && !attachment)) {
                if (callback) callback({ status: 'error', message: 'Invalid payload' });
                return;
            }

            const cleanMsg = (message || '').trim();
            const cleanAttachment = attachment || null;
            const now = new Date();
            const formattedDate = now.toISOString().slice(0, 19).replace('T', ' ');

            const roomName = `company_${company_id}`;
            const roomState = activeRooms.get(roomName) || { adminCount: 0, companyCount: 0 };

            // Determine initial seen status based on active presence
            let isSeenUser = message_by === 'company' ? 1 : (roomState.companyCount > 0 ? 1 : 0);
            let isSeenAdmin = message_by === 'admin' ? 1 : (roomState.adminCount > 0 ? 1 : 0);

            const senderId = sender || (message_by === 'admin' ? 1 : company_id);
            const receiverId = receiver || (message_by === 'admin' ? company_id : 1);

            const [result] = await pool.execute(
                `INSERT INTO chats (company_id, sender, receiver, message, attachment, message_by, is_seen_user, is_seen_admin, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                [company_id, senderId, receiverId, cleanMsg, cleanAttachment, message_by, isSeenUser, isSeenAdmin, formattedDate, formattedDate]
            );

            const messageObj = {
                id: result.insertId,
                company_id: parseInt(company_id),
                sender: senderId,
                receiver: receiverId,
                message: cleanMsg,
                attachment: cleanAttachment,
                message_by: message_by,
                is_seen_user: isSeenUser,
                is_seen_admin: isSeenAdmin,
                created_at: formattedDate
            };

            // Broadcast to the whole room
            io.to(roomName).emit('new_message', messageObj);

            // Also broadcast global update for admin chat list if sent by company
            io.emit('chat_list_activity', {
                company_id: parseInt(company_id),
                last_message: cleanAttachment ? '📷 [Image attachment]' : cleanMsg,
                message_by: message_by,
                timestamp: formattedDate
            });

            if (callback) callback({ status: 'ok', data: messageObj });
        } catch (err) {
            console.error('[Error saving message]:', err.message);
            if (callback) callback({ status: 'error', message: err.message });
        }
    });

    // Broadcast pre-saved message (e.g. uploaded via Laravel HTTP controller)
    socket.on('broadcast_message', (data, callback) => {
        const { company_id, messageObj } = data || {};
        if (company_id && messageObj) {
            const roomName = `company_${company_id}`;
            io.to(roomName).emit('new_message', messageObj);
            io.emit('chat_list_activity', {
                company_id: parseInt(company_id),
                last_message: messageObj.attachment ? '📷 [Image attachment]' : messageObj.message,
                message_by: messageObj.message_by,
                timestamp: messageObj.created_at || new Date().toISOString()
            });
            if (callback) callback({ status: 'ok' });
        }
    });

    // Real-time typing indicator
    socket.on('typing', (data) => {
        const { company_id, role } = data || {};
        if (company_id) {
            socket.to(`company_${company_id}`).emit('user_typing', {
                company_id: company_id,
                role: role,
                is_typing: true
            });
        }
    });

    socket.on('stop_typing', (data) => {
        const { company_id, role } = data || {};
        if (company_id) {
            socket.to(`company_${company_id}`).emit('user_typing', {
                company_id: company_id,
                role: role,
                is_typing: false
            });
        }
    });

    // Mark as seen
    socket.on('mark_seen', async (data) => {
        try {
            const { company_id, role } = data || {};
            if (!company_id || !role) return;

            if (role === 'admin') {
                await pool.execute(
                    "UPDATE chats SET is_seen_admin = 1 WHERE company_id = ? AND message_by = 'company'",
                    [company_id]
                );
            } else if (role === 'company') {
                await pool.execute(
                    "UPDATE chats SET is_seen_user = 1 WHERE company_id = ? AND message_by = 'admin'",
                    [company_id]
                );
            }

            io.to(`company_${company_id}`).emit('messages_seen', {
                company_id: company_id,
                seen_by: role
            });
        } catch (err) {
            console.error('[Error marking seen]:', err.message);
        }
    });

    // Close / Resolve or Reopen Chat
    socket.on('update_chat_status', async (data, callback) => {
        try {
            const { company_id, status, role, reason } = data || {};
            if (!company_id || !status) return;

            await pool.execute(
                "UPDATE company_details SET chat_status = ? WHERE id = ?",
                [status, company_id]
            );

            const cleanReason = reason ? ` (Reason: ${reason})` : '';
            const actionText = status === 'closed'
                ? `🔒 Support session was marked as Resolved & Closed by ${role === 'admin' ? 'Super Admin' : 'Company'}.${cleanReason}`
                : `🔓 Support session was Reopened by ${role === 'admin' ? 'Super Admin' : 'Company'}.`;

            const now = new Date();
            const formattedDate = now.toISOString().slice(0, 19).replace('T', ' ');

            const [result] = await pool.execute(
                `INSERT INTO chats (company_id, sender, receiver, message, message_by, is_seen_user, is_seen_admin, created_at, updated_at) 
                 VALUES (?, 1, ?, ?, 'system', 1, 1, ?, ?)`,
                [company_id, company_id, actionText, formattedDate, formattedDate]
            );

            const systemMsgObj = {
                id: result.insertId,
                company_id: parseInt(company_id),
                sender: 1,
                receiver: parseInt(company_id),
                message: actionText,
                message_by: 'system',
                is_seen_user: 1,
                is_seen_admin: 1,
                created_at: formattedDate
            };

            const roomName = `company_${company_id}`;
            io.to(roomName).emit('chat_status_updated', {
                company_id: parseInt(company_id),
                status: status,
                updated_by: role,
                system_message: systemMsgObj
            });

            io.emit('chat_list_activity', {
                company_id: parseInt(company_id),
                last_message: actionText,
                message_by: 'system',
                chat_status: status,
                timestamp: formattedDate
            });

            if (callback) callback({ status: 'ok', data: { chat_status: status } });
        } catch (err) {
            console.error('[Error updating chat status]:', err.message);
            if (callback) callback({ status: 'error', message: err.message });
        }
    });

    // Disconnect cleanup
    socket.on('disconnect', () => {
        console.log(`[Socket Disconnected] ID: ${socket.id}`);
        if (currentRoom && activeRooms.has(currentRoom)) {
            const roomState = activeRooms.get(currentRoom);
            roomState.sockets.delete(socket.id);
            if (userRole === 'admin' && roomState.adminCount > 0) roomState.adminCount--;
            if (userRole === 'company' && roomState.companyCount > 0) roomState.companyCount--;

            io.to(currentRoom).emit('presence_update', {
                company_id: userCompanyId,
                admin_online: roomState.adminCount > 0,
                company_online: roomState.companyCount > 0
            });

            if (roomState.sockets.size === 0) {
                activeRooms.delete(currentRoom);
            }
        }
    });
});

server.listen(PORT, '127.0.0.1', () => {
    console.log(`🚀 Stafo Real-time Chat WebSocket Server running on http://127.0.0.1:${PORT}`);
});
