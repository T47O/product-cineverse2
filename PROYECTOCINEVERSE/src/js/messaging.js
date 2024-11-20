document.addEventListener('DOMContentLoaded', () => {
    const mutualFollowersList = document.getElementById('mutualFollowersList');
    const chatHeader = document.getElementById('chatHeader');
    const messagesList = document.getElementById('messagesList');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');

    let selectedUserId = null;
    let selectedUserName = '';
    let lastMessageId = 0;
    let pollingInterval;

    function fetchMutualFollowers() {
        fetch(`../API_FOLLOW/get_mutual_followers.php?id_usuario=${currentUserId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    mutualFollowersList.innerHTML = '';
                    data.mutual_followers.forEach(follower => {
                        const li = document.createElement('li');
                        li.textContent = follower.Nombre;
                        li.onclick = () => selectUser(follower);
                        mutualFollowersList.appendChild(li);
                    });
                } else {
                    throw new Error(data.message || 'Error fetching mutual followers');
                }
            })
            .catch(error => {
                console.error('Error fetching mutual followers:', error);
                alert('Error al cargar seguidores mutuos. Por favor, intenta de nuevo.');
            });
    }

    function selectUser(user) {
        selectedUserId = user.ID_Usuario;
        selectedUserName = user.Nombre;
        chatHeader.textContent = `Chat con @${user.ID_Usuario} - ${user.Nombre}  `;
        lastMessageId = 0;
        messagesList.innerHTML = '';
        fetchMessages();
        startPolling();
    }

    function fetchMessages() {
        if (!selectedUserId) return;
        fetch(`../API_MENSAJERIA/get_messages.php?id_emisor=${currentUserId}&id_receptor=${selectedUserId}&last_id=${lastMessageId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    data.messages.forEach(message => {
                        if (message.ID_Mensaje > lastMessageId) {
                            lastMessageId = message.ID_Mensaje;
                            appendMessage(message);
                        }
                    });
                    if (lastMessageId === 0) {
                        messagesList.scrollTop = messagesList.scrollHeight;
                    }
                } else {
                    throw new Error(data.message || 'Error fetching messages');
                }
            })
            .catch(error => {
                console.error('Error fetching messages:', error);
                alert('Error al cargar mensajes. Por favor, intenta de nuevo.');
            });
    }

    function appendMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message');
        messageDiv.classList.add(message.ID_Emisor == currentUserId ? 'sent' : 'received');
        messageDiv.dataset.messageId = message.ID_Mensaje;
        
        const contentP = document.createElement('p');
        contentP.textContent = message.Contenido;
        messageDiv.appendChild(contentP);
        
        const timeSpan = document.createElement('span');
        timeSpan.classList.add('message-time');
        timeSpan.textContent = new Date(message.Fecha_Hora).toLocaleTimeString();
        messageDiv.appendChild(timeSpan);
        
        if (message.ID_Emisor == currentUserId) {
            const optionsButton = document.createElement('button');
            optionsButton.classList.add('message-options');
            optionsButton.innerHTML = '&#8942;'; // Código HTML para el ícono de tres puntos verticales
            optionsButton.setAttribute('aria-label', 'Opciones del mensaje');
            optionsButton.onclick = (event) => showMessageOptions(event, message.ID_Mensaje);
            messageDiv.appendChild(optionsButton);
        }
        
        messagesList.appendChild(messageDiv);
        messagesList.scrollTop = messagesList.scrollHeight;
    }

    function showMessageOptions(event, messageId) {
        event.stopPropagation();
        const existingMenu = document.querySelector('.message-options-menu');
        if (existingMenu) {
            existingMenu.remove();
        }

        const optionsMenu = document.createElement('div');
        optionsMenu.classList.add('message-options-menu');
        
        const deleteOption = document.createElement('button');
        deleteOption.textContent = 'Eliminar mensaje';
        deleteOption.onclick = () => deleteMessage(messageId);
        
        optionsMenu.appendChild(deleteOption);
        event.target.parentNode.appendChild(optionsMenu);

        // Cerrar el menú al hacer clic fuera de él
        document.addEventListener('click', closeMessageOptions);
    }

    function closeMessageOptions(event) {
        if (!event.target.closest('.message-options-menu') && !event.target.closest('.message-options')) {
            const menu = document.querySelector('.message-options-menu');
            if (menu) {
                menu.remove();
            }
            document.removeEventListener('click', closeMessageOptions);
        }
    }

    function sendMessage() {
        if (!selectedUserId || !messageInput.value.trim()) return;
        
        const messageData = {
            id_emisor: currentUserId,
            id_receptor: selectedUserId,
            contenido: messageInput.value.trim()
        };

        fetch('../API_MENSAJERIA/send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(messageData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                fetchMessages();
            } else {
                throw new Error(data.message || 'Error sending message');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error al enviar el mensaje. Por favor, intenta de nuevo.');
        });
    }

    function deleteMessage(messageId) {
        fetch('../API_MENSAJERIA/delete_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_mensaje: messageId,
                id_usuario: currentUserId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const messageElement = document.querySelector(`.message[data-message-id="${messageId}"]`);
                if (messageElement) {
                    messageElement.remove();
                }
                closeMessageOptions({ target: document.body });
            } else {
                throw new Error(data.message || 'Error deleting message');
            }
        })
        .catch(error => {
            console.error('Error deleting message:', error);
            alert('Error al eliminar el mensaje. Por favor, intenta de nuevo.');
        });
    }

    function startPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        pollingInterval = setInterval(fetchMessages, 5000); // Poll every 5 seconds
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    }

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    fetchMutualFollowers();

    // Stop polling when the user leaves the page
    window.addEventListener('beforeunload', stopPolling);
});