document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de registro
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const repeatPassword = document.getElementById('repeat-password').value;
            const errorMessage = document.getElementById('error-message');

            if (password !== repeatPassword) {
                errorMessage.textContent = 'Las contraseñas no coinciden';
                return;
            }

            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries());

            fetch('../API USUARIO/api.php/usuarios', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    alert('Usuario registrado con éxito');
                    window.location.href = 'login.html';
                } else {
                    errorMessage.textContent = result.message || 'Error al registrar el usuario';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = 'Error en la conexión: ' + error.message;
            });
        });
    }

    // Funcionalidad de login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());
            const errorMessage = document.getElementById('error-message');

            fetch('../API USUARIO/api.php/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    alert('Inicio de sesión exitoso');
                    window.location.href = '../php/perfil.php';
                } else {
                    errorMessage.textContent = result.message || 'Error al iniciar sesión';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = 'Error en la conexión: ' + error.message;
            });
        });
    }

    // Funcionalidad de búsqueda
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = document.getElementById('searchInput').value;
            // Implementar la lógica de búsqueda aquí
            console.log('Búsqueda realizada:', searchTerm);
            // Aquí puedes agregar una llamada a la API para realizar la búsqueda
        });
    }

    // Funcionalidad de navegación
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            // Implementar la lógica de navegación aquí
            console.log('Navegando a:', page);
            // Aquí puedes agregar la lógica para cambiar de página
        });
    });

    // Funcionalidad de logout
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('../API USUARIO/api.php/logout', {
                method: 'POST',
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    alert('Sesión cerrada con éxito');
                    window.location.href = 'login.html';
                } else {
                    alert('Error al cerrar sesión');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error en la conexión: ' + error.message);
            });
        });
    }

    // Funcionalidad para mostrar/ocultar contraseña
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
        });
    });

    // Funcionalidad para el carrusel de películas/series
    const carousel = document.querySelector('.carousel');
    if (carousel) {
        const prevButton = carousel.querySelector('.prev');
        const nextButton = carousel.querySelector('.next');
        const items = carousel.querySelectorAll('.carousel-item');
        let currentIndex = 0;

        function showItem(index) {
            items.forEach((item, i) => {
                item.style.display = i === index ? 'block' : 'none';
            });
        }

        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            showItem(currentIndex);
        });

        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % items.length;
            showItem(currentIndex);
        });

        showItem(currentIndex);
    }

    // Funcionalidad para calificar películas/series
    const ratingStars = document.querySelectorAll('.rating-star');
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            const movieId = this.closest('.movie-item').getAttribute('data-movie-id');
            // Implementar la lógica para enviar la calificación al servidor
            console.log(`Película ${movieId} calificada con ${rating} estrellas`);
            // Aquí puedes agregar una llamada a la API para guardar la calificación
        });
    });

    // Funcionalidad para agregar comentarios
    const commentForms = document.querySelectorAll('.comment-form');
    commentForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const movieId = this.getAttribute('data-movie-id');
            const commentText = this.querySelector('textarea').value;
            // Implementar la lógica para enviar el comentario al servidor
            console.log(`Comentario para la película ${movieId}: ${commentText}`);
            // Aquí puedes agregar una llamada a la API para guardar el comentario
        });
    });

    // Funcionalidad para compartir películas/series
    const shareButtons = document.querySelectorAll('.share-button');
    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const movieId = this.getAttribute('data-movie-id');
            const movieTitle = this.getAttribute('data-movie-title');
            // Implementar la lógica para compartir la película/serie
            console.log(`Compartiendo: ${movieTitle} (ID: ${movieId})`);
            // Aquí puedes implementar la funcionalidad de compartir, por ejemplo, usando la Web Share API
            if (navigator.share) {
                navigator.share({
                    title: movieTitle,
                    text: `¡Mira esta película en Cineverse!`,
                    url: `https://cineverse.com/movie/${movieId}`
                }).then(() => {
                    console.log('Contenido compartido con éxito');
                }).catch((error) => {
                    console.log('Error al compartir', error);
                });
            } else {
                alert('La funcionalidad de compartir no está disponible en este navegador');
            }
        });
    });

    // Funcionalidad para actualizar preferencias de usuario
    const preferencesForm = document.getElementById('preferencesForm');
    if (preferencesForm) {
        preferencesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(preferencesForm);
            const preferences = Object.fromEntries(formData.entries());
            // Implementar la lógica para enviar las preferencias al servidor
            console.log('Preferencias actualizadas:', preferences);
            // Aquí puedes agregar una llamada a la API para guardar las preferencias
        });
    }

    // Funcionalidad para mostrar recomendaciones personalizadas
    function loadRecommendations() {
        // Implementar la lógica para cargar recomendaciones personalizadas
        console.log('Cargando recomendaciones personalizadas');
        // Aquí puedes agregar una llamada a la API para obtener recomendaciones
        // y luego actualizar el DOM con los resultados
    }

    // Llamar a loadRecommendations cuando sea necesario, por ejemplo:
    // loadRecommendations();

    // Funcionalidad para manejar la lista de reproducción del usuario
    function updatePlaylist(action, movieId) {
        // Implementar la lógica para actualizar la lista de reproducción
        console.log(`${action} película ${movieId} en la lista de reproducción`);
        // Aquí puedes agregar una llamada a la API para actualizar la lista de reproducción
    }

    const playlistButtons = document.querySelectorAll('.playlist-button');
    playlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.textContent.trim().toLowerCase();
            const movieId = this.getAttribute('data-movie-id');
            updatePlaylist(action, movieId);
            this.textContent = action === 'agregar' ? 'Quitar de la lista' : 'Agregar a la lista';
        });
    });
});