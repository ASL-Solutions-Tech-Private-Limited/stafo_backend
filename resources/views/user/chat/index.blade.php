@extends('user.layouts.app')
@section('title', 'Chat List') <!-- Set your custom title here -->

@section('css')
<style>
 
        .chat-container {
            width: 600px;
            height: 500px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            border-bottom: 1px solid #ddd;
        }

        .message {
            margin-bottom: 15px;
        }

        .message span {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .message p {
            margin: 0;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 10px;
            max-width: 100%;
        }

        .user p {
            background-color:rgb(38, 149, 253);
            color: #fff;
            text-align: right;
        }

        .bot p {
            background-color: #f1f1f1;
            color: #333;
            text-align: left;
        }

        .input-container {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .input-container input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 10px;
        }

        .input-container button {
            background-color: #0084ff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
        }

        .input-container button:hover {
            background-color: #006bb3;
        }
    </style>
@endsection

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0 emp-data">
        <div class="card-header">
            <h3 class="card-title">Message to Admin</h3>
        </div>
        <div class="container">
            <div class="chat-container">
                <div class="chat-box" id="chatBox">
                @if(count($chats) > 0)
                        @foreach($chats as $chat)
                                <div class="message {{ $chat->message_by == 'company' ? 'user' : 'bot' }}">
                                    <!-- <span>{{ $chat->sender }}</span> -->
                                    <p>{{ $chat->message }}</p>
                                </div>
                        @endforeach
                        @else   
                            <div class="message bot">
                                <!-- <span>Bot</span> -->
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        @endif
                </div>
                <div class="input-container">
                    <input type="text" id="userInput" placeholder="Type a message...">
                    <button onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
        function sendMessage() {
            const userInput = document.getElementById('userInput');
            const chatBox = document.getElementById('chatBox');
            const messageText = userInput.value.trim();
            const token = '{{ csrf_token() }}';

            if (messageText) {
                const userMessage = document.createElement('div');
                userMessage.classList.add('message', 'user');
                userMessage.innerHTML = `                    
                    <p>${messageText}</p>
                `;
                chatBox.appendChild(userMessage);
                userInput.value = '';

                // Scroll to bottom of the chat
                chatBox.scrollTop = chatBox.scrollHeight;

                $.ajax({
                    url: '{{ url('/') }}' + '/save-chat/',
                    type: 'GET',
                    dataType: 'json',
                    data:{message:messageText,_token:token},
                    success: function(data) {
                        if (data) {
                            
                        }
                    }
                });

                // Simulate bot response
                setTimeout(() => {
                    // const botMessage = document.createElement('div');
                    // botMessage.classList.add('message', 'bot');
                    // botMessage.innerHTML = `
                    //     <span>Bot</span>
                    //     <p>Thank you for your message! How else can I help?</p>
                    // `;
                    // chatBox.appendChild(botMessage);
                    // chatBox.scrollTop = chatBox.scrollHeight;
                }, 1000);
            }
        }

        setInterval(() => {
            $.ajax({
                url: '{{ url('/') }}' + '/get-chat/',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        const chatBox = document.getElementById('chatBox');
                        chatBox.innerHTML = ''; // Clear existing messages
                        data.forEach(chat => {
                            const message = document.createElement('div');
                            message.classList.add('message', chat.message_by == 'company' ? 'user' : 'bot');
                            message.innerHTML = `
                                <p>${chat.message}</p>
                            `;
                            chatBox.appendChild(message);
                        });
                        chatBox.scrollTop = chatBox.scrollHeight; // Scroll to bottom
                    }
                }})}, 1000);
    </script>
@endsection

