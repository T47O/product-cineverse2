document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    function performSearch() {
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            fetch(`../API USUARIO/api.php/usuarios/search?term=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.user) {
                        displayUserInfo(data.user);
                    } else {
                        showPopup('No se encontró ningún usuario con ese ID');
                    }
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    showPopup('Error al buscar usuarios');
                });
        }
    }

    function displayUserInfo(user) {
        const isOwnProfile = user.ID_Usuario == userId; // Asumiendo que userId está definido globalmente
        const userInfoHTML = `
            <div class="user-info-popup">
                <h2>${isOwnProfile ? 'Tu perfil' : 'Usuario encontrado'}:</h2>
                <p>ID: ${user.ID_Usuario}</p>
                <p>Nombre: ${user.Nombre}</p>
                ${isOwnProfile ? '' : `<a href="perfil.php?id=${user.ID_Usuario}" class="view-profile-btn">Ver perfil completo</a>`}
                <button class="close-popup-btn">Cerrar</button>
            </div>
        `;
        showPopup(userInfoHTML);
    }

    function showPopup(content) {
        searchResults.innerHTML = content;
        searchResults.style.display = 'block';

        const closeBtn = searchResults.querySelector('.close-popup-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                searchResults.style.display = 'none';
            });
        }
    }

    searchButton.addEventListener('click', performSearch);

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Cerrar el popup si se hace clic fuera de él
    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && e.target !== searchButton && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });
});