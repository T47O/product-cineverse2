document.addEventListener('DOMContentLoaded', function() {
    const postsContainer = document.getElementById('postsContainer');

    // Función para cargar los posts
    function loadPosts() {
        fetch('../API POST/list_post.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', text);
                    throw new Error('Invalid JSON response');
                }
            })
            .then(data => {
                if (data.success) {
                    postsContainer.innerHTML = '';
                    if (data.posts && data.posts.length > 0) {
                        data.posts.forEach(post => {
                            createPostElement(post);
                        });
                    } else {
                        postsContainer.innerHTML = '<div class="no-posts">No hay publicaciones disponibles</div>';
                    }
                } else {
                    console.error('Error al cargar los posts:', data.message);
                    postsContainer.innerHTML = '<div class="error-message">Error al cargar las publicaciones</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                postsContainer.innerHTML = '<div class="error-message">Error al cargar las publicaciones</div>';
            });
    }

    // Función para crear el elemento HTML de un post
    function createPostElement(post) {
        const postElement = document.createElement('div');
        postElement.className = 'post';
        postElement.innerHTML = `
            <div class="post-header">
                <a class="post-username" href="../php/perfil.php?id=${post.ID_Usuario}"><span class="post-username">${post.NombreUsuario || 'Usuario ' + post.ID_Usuario}</span></a>
                ${post.ID_Usuario == userId ? `
                    <div class="post-options">
                        <button class="options-button" aria-label="Opciones de publicación">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="options-menu" style="display: none;">
                            <button class="delete-post" data-post-id="${post.ID_Publicacion}">Eliminar</button>
                        </div>
                    </div>
                ` : ''}
            </div>
            <div class="post-image-container">
                <img src="../API POST/get_post_image.php?id_publicacion=${post.ID_Publicacion}" 
                     alt="Post" class="post-image"
                     onerror="this.src='../img/default-img.jpg'">
            </div>
            <div class="post-content">
                <h3 class="post-title">${post.Titulo || ''}</h3>
                <p class="post-description">${post.Descripcion || ''}</p>
                <div class="post-actions">
                    <div class="post-action" onclick="toggleLike(${post.ID_Publicacion})">
                        <i class="fas fa-heart" id="like-${post.ID_Publicacion}"></i>
                        <span id="likes-count-${post.ID_Publicacion}">0</span>
                    </div>
                    <div class="post-action" onclick="toggleComments(${post.ID_Publicacion})">
                        <i class="fas fa-comment"></i>
                        <span id="comments-count-${post.ID_Publicacion}">0</span>
                    </div>
                </div>
                <div class="comments-section" id="comments-${post.ID_Publicacion}" style="display: none;">
                    <div class="comments-list" id="comments-list-${post.ID_Publicacion}"></div>
                    <div class="comment-form">
                        <input type="text" placeholder="Añade un comentario..." 
                               id="comment-input-${post.ID_Publicacion}">
                        <button class="comment-submit" onclick="addComment(${post.ID_Publicacion})">Enviar</button>
                    </div>
                </div>
            </div>
        `;
        postsContainer.appendChild(postElement);
        loadComments(post.ID_Publicacion);
        loadLikes(post.ID_Publicacion);

        // Agregar event listener para el menú de opciones
        if (post.ID_Usuario == userId) {
            const optionsButton = postElement.querySelector('.options-button');
            const optionsMenu = postElement.querySelector('.options-menu');
            const deleteButton = postElement.querySelector('.delete-post');

            optionsButton.addEventListener('click', () => {
                optionsMenu.style.display = optionsMenu.style.display === 'none' ? 'block' : 'none';
            });

            deleteButton.addEventListener('click', () => {
                deletePost(post.ID_Publicacion);
            });
        }
    }

    // Función para eliminar un post
    function deletePost(postId) {
        if (confirm('¿Estás seguro de que quieres eliminar esta publicación?')) {
            fetch('../API POST/delete_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_publicacion=${postId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Eliminar el post del DOM
                    const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
                    if (postElement) {
                        postElement.remove();
                    }
                    alert('Publicación eliminada con éxito');
                    location.reload(true);
                } else {
                    alert('Error al eliminar la publicación: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la publicación');
            });
        }
    }

    // Función para cargar comentarios
    function loadComments(postId) {
        fetch(`../API_COMENTARIOS/get_comments.php?id_publicacion=${postId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const commentsContainer = document.getElementById(`comments-list-${postId}`);
                    const commentsCount = document.getElementById(`comments-count-${postId}`);
                    if (commentsContainer && commentsCount) {
                        commentsCount.textContent = data.comments.length;

                        commentsContainer.innerHTML = data.comments.map(comment => `
                            <div class="comment">
                                <span class="comment-author">${comment.Autor}</span>
                                <span class="comment-content">${comment.Contenido}</span>
                                ${parseInt(comment.ID_Usuario) === parseInt(userId) ? 
                                    `<button class="delete-comment" onclick="deleteComment(${comment.ID_Comenta}, ${postId})">
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
    window.addComment = function(postId) {
        const commentInput = document.getElementById(`comment-input-${postId}`);
        const content = commentInput.value.trim();
        
        if (content) {
            const formData = new FormData();
            formData.append('id_usuario', userId);
            formData.append('id_publicacion', postId);
            formData.append('contenido', content);

            fetch('../API_COMENTARIOS/create_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentInput.value = '';
                    loadComments(postId);
                }
            })
            .catch(error => console.error('Error al crear comentario:', error));
        }
    }

    // Función para eliminar comentario
    window.deleteComment = function(commentId, postId) {
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
                    loadComments(postId);
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
    window.toggleComments = function(postId) {
        const commentsSection = document.getElementById(`comments-${postId}`);
        if (commentsSection) {
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Función para dar/quitar like
    window.toggleLike = function(postId) {
        const likeIcon = document.getElementById(`like-${postId}`);
        const likesCount = document.getElementById(`likes-count-${postId}`);
        
        fetch('../API_LIKE/toggle_like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_usuario: userId,
                id_publicacion: postId
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
    function loadLikes(postId) {
        fetch(`../API_LIKE/get_likes.php?id_publicacion=${postId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likesCount = document.getElementById(`likes-count-${postId}`);
                    if (likesCount) {
                        likesCount.textContent = data.total_likes;
                    }
                }
            })
            .catch(error => console.error('Error cargando likes:', error));
    }

    // Cargar posts al iniciar
    loadPosts();
});