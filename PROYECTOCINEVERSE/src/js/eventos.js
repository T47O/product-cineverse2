document.addEventListener('DOMContentLoaded', function() {
    const eventosContainer = document.getElementById('eventosContainer');

    // Función para cargar los eventos
    function loadEventos() {
        fetch('../API_EVENTO/list_events.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    eventosContainer.innerHTML = '';
                    if (data.events && data.events.length > 0) {
                        data.events.forEach(evento => {
                            createEventoElement(evento);
                        });
                    } else {
                        eventosContainer.innerHTML = '<div class="no-eventos">No hay eventos disponibles</div>';
                    }
                } else {
                    console.error('Error al cargar los eventos:', data.message);
                    eventosContainer.innerHTML = '<div class="error-message">Error al cargar los eventos</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                eventosContainer.innerHTML = '<div class="error-message">Error al cargar los eventos</div>';
            });
    }

    // Función para crear el elemento HTML de un evento
    function createEventoElement(evento) {
        const eventoElement = document.createElement('div');
        eventoElement.className = 'evento';
        eventoElement.dataset.eventoId = evento.ID_Evento;
        eventoElement.innerHTML = `
            <div class="evento-header">
                <a class="evento-username" href="../php/perfil.php?id=${userId} ><span class="evento-username">${evento.NombreUsuario || 'Usuario desconocido'}</span></a>
                ${parseInt(evento.ID_Usuario) === parseInt(userId) ? `
                    <div class="evento-options">
                        <button class="options-button" aria-label="Opciones de evento">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="options-menu" style="display: none;">
                            <button class="delete-evento" data-evento-id="${evento.ID_Evento}">Eliminar</button>
                        </div>
                    </div>
                ` : ''}
            </div>
            <div class="evento-image-container">
                <img src="../API POST/get_post_image.php?id_publicacion=${evento.ID_Publicacion}" 
                     alt="Evento" class="evento-image"
                     onerror="this.src='../img/default-event-img.jpg'">
            </div>
            <div class="evento-content">
                <h3 class="evento-title">${evento.Titulo || ''}</h3>
                <p class="evento-description">${evento.Descripcion || ''}</p>
                <p class="evento-datetime">Fecha y hora: ${evento.FechaHora_Realizacion || ''}</p>
                <p class="evento-location">Ubicación: ${evento.Ubicacion || ''}</p>
                <div class="evento-actions">
                    <div class="evento-action" onclick="toggleLike(${evento.ID_Publicacion})">
                        <i class="fas fa-heart" id="like-${evento.ID_Publicacion}"></i>
                        <span id="likes-count-${evento.ID_Publicacion}">0</span>
                    </div>
                    <div class="evento-action" onclick="toggleComments(${evento.ID_Publicacion})">
                        <i class="fas fa-comment"></i>
                        <span id="comments-count-${evento.ID_Publicacion}">0</span>
                    </div>
                </div>
                <div class="comments-section" id="comments-${evento.ID_Publicacion}" style="display: none;">
                    <div class="comments-list" id="comments-list-${evento.ID_Publicacion}"></div>
                    <div class="comment-form">
                        <input type="text" placeholder="Añade un comentario..." 
                               id="comment-input-${evento.ID_Publicacion}">
                        <button class="comment-submit" onclick="addComment(${evento.ID_Publicacion})">Enviar</button>
                    </div>
                </div>
            </div>
        `;
        eventosContainer.appendChild(eventoElement);
        loadComments(evento.ID_Publicacion);
        loadLikes(evento.ID_Publicacion);

        // Agregar event listeners para el menú de opciones
        if (parseInt(evento.ID_Usuario) === parseInt(userId)) {
            const optionsButton = eventoElement.querySelector('.options-button');
            const optionsMenu = eventoElement.querySelector('.options-menu');
            const deleteButton = eventoElement.querySelector('.delete-evento');

            optionsButton.addEventListener('click', (e) => {
                e.stopPropagation();
                optionsMenu.style.display = optionsMenu.style.display === 'none' ? 'block' : 'none';
            });

            deleteButton.addEventListener('click', (e) => {
                e.stopPropagation();
                deleteEvento(evento.ID_Evento);
            });

            // Cerrar el menú si se hace clic fuera de él
            document.addEventListener('click', () => {
                optionsMenu.style.display = 'none';
            });
        }
    }

    // Función para eliminar un evento
    function deleteEvento(eventoId) {
        if (confirm('¿Estás seguro de que quieres eliminar este evento?')) {
            fetch('../API_EVENTO/delete_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_evento: eventoId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const eventoElement = document.querySelector(`.evento[data-evento-id="${eventoId}"]`);
                    if (eventoElement) {
                        eventoElement.remove();
                    }
                    alert('Evento eliminado con éxito');
                } else {
                    alert('Error al eliminar el evento: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el evento');
            });
        }
    }

    // Función para cargar comentarios
    function loadComments(eventoId) {
        fetch(`../API_COMENTARIOS/get_comments.php?id_publicacion=${eventoId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const commentsContainer = document.getElementById(`comments-list-${eventoId}`);
                    const commentsCount = document.getElementById(`comments-count-${eventoId}`);
                    if (commentsContainer && commentsCount) {
                        commentsCount.textContent = data.comments.length;

                        commentsContainer.innerHTML = data.comments.map(comment => `
                            <div class="comment">
                                <span class="comment-author">${comment.Autor}</span>
                                <span class="comment-content">${comment.Contenido}</span>
                                ${parseInt(comment.ID_Usuario) === parseInt(userId) ? 
                                    `<button class="delete-comment" onclick="deleteComment(${comment.ID_Comenta}, ${eventoId})">
                                        <i class="fas fa-trash"></i>
                                    </button>` : ''}
                            </div>
                        `).join('');
                    }
                }
            })
            .catch(error => console.error('Error cargando comentarios:', error));
    }

    // Función para añadir comentario
    window.addComment = function(eventoId) {
        const commentInput = document.getElementById(`comment-input-${eventoId}`);
        const content = commentInput.value.trim();
        
        if (content) {
            const formData = new FormData();
            formData.append('id_usuario', userId);
            formData.append('id_publicacion', eventoId);
            formData.append('contenido', content);

            fetch('../API_COMENTARIOS/create_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentInput.value = '';
                    loadComments(eventoId);
                }
            })
            .catch(error => console.error('Error al crear comentario:', error));
        }
    }

    // Función para eliminar comentario
    window.deleteComment = function(commentId, eventoId) {
        if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
            const formData = new FormData();
            formData.append('id_comenta', commentId);
            formData.append('id_usuario', userId);

            fetch('../API_COMENTARIOS/delete_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadComments(eventoId);
                } else {
                    alert('Error al eliminar el comentario: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error al eliminar comentario:', error);
                alert('Error al eliminar el comentario');
            });
        }
    }

    // Función para mostrar/ocultar comentarios
    window.toggleComments = function(eventoId) {
        const commentsSection = document.getElementById(`comments-${eventoId}`);
        if (commentsSection) {
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Función para dar/quitar like
    window.toggleLike = function(eventoId) {
        const likeIcon = document.getElementById(`like-${eventoId}`);
        const likesCount = document.getElementById(`likes-count-${eventoId}`);
        
        fetch('../API_LIKE/toggle_like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_usuario: userId,
                id_publicacion: eventoId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                likeIcon.classList.toggle('liked');
                likesCount.textContent = data.likes_count;
            }
        })
        .catch(error => console.error('Error al dar/quitar like:', error));
    }

    // Función para cargar el número de likes
    function loadLikes(eventoId) {
        fetch(`../API_LIKE/get_likes.php?id_publicacion=${eventoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likesCount = document.getElementById(`likes-count-${eventoId}`);
                    if (likesCount) {
                        likesCount.textContent = data.total_likes;
                    }
                }
            })
            .catch(error => console.error('Error cargando likes:', error));
    }

    // Cargar eventos al iniciar
    loadEventos();
});