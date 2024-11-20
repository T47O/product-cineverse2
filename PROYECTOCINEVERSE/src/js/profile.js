document.addEventListener('DOMContentLoaded', function() {
    const settingsButton = document.getElementById('settingsButton');
    const settingsMenu = document.getElementById('settingsMenu');
    const settingsForm = document.getElementById('settingsForm');
    const profilePicture = document.getElementById('profilePicture');
    const followersCount = document.getElementById('followersCount');
    const followingCount = document.getElementById('followingCount');
    const followButton = document.getElementById('followButton');
    const newBirthdate = document.getElementById('newBirthdate');

    if (isOwnProfile) {
        if (settingsButton) {
            settingsButton.addEventListener('click', function() {
                settingsMenu.classList.toggle('active');
            });
        }

        // Validación de edad
        newBirthdate.addEventListener('change', function() {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            if (age < 16) {
                alert('Debes tener al menos 16 años para usar esta plataforma.');
                this.value = ''; // Limpiar el campo
            }
        });

        // Añadir funcionalidad para cambiar la foto desde la interfaz de perfil
        const profilePictureContainer = document.querySelector('.profile-picture-container');

        if (profilePictureContainer) {
            const changePhotoOverlay = document.createElement('div');
            changePhotoOverlay.className = 'change-photo-overlay';
            changePhotoOverlay.innerHTML = '<i class="fas fa-plus"></i>';
            profilePictureContainer.appendChild(changePhotoOverlay);

            profilePictureContainer.addEventListener('mouseover', function() {
                changePhotoOverlay.style.opacity = '1';
            });

            profilePictureContainer.addEventListener('mouseout', function() {
                changePhotoOverlay.style.opacity = '0';
            });

            changePhotoOverlay.addEventListener('click', function() {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.onchange = function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const formData = new FormData();
                        formData.append('Foto_perfil', file);

                        fetch('../API USUARIO/upload_profile_picture.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error('Error en la respuesta del servidor: ' + text);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Actualizar la imagen de perfil sin recargar la página
                                document.getElementById('profilePicture').src = 'get_profile_picture.php?id=' + userId + '&t=' + new Date().getTime();
                                alert('Imagen de perfil actualizada con éxito');
                            } else {
                                throw new Error(data.message || 'Error desconocido al subir la imagen');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al subir la imagen: ' + error.message);
                        });
                    }
                };
                input.click();
            });
        }

        if (settingsForm) {
            settingsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nombre = document.getElementById('newName').value;
                const email = document.getElementById('newEmail').value;
                const contrasenia = document.getElementById('newPassword').value;
                const fechaNacimiento = document.getElementById('newBirthdate').value;
                const descripcion = document.getElementById('newDescription').value;

                // Validar campos obligatorios
                if (!nombre || !email || !fechaNacimiento) {
                    alert('El nombre, email y fecha de nacimiento son obligatorios');
                    return;
                }

                // Validar email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Por favor, introduce un email válido');
                    return;
                }

                // Validar contraseña solo si se ha introducido
                if (contrasenia && (contrasenia.length < 8 || !/[A-Z]/.test(contrasenia))) {
                    alert('La contraseña debe tener al menos 8 caracteres y una letra mayúscula');
                    return;
                }

                // Validar descripción
                if (descripcion.length > 50) {
                    alert('La descripción no puede tener más de 50 caracteres');
                    return;
                }

                // Si todas las validaciones pasan, enviar el formulario
                const formData = new FormData(settingsForm);
                
                fetch(`../API USUARIO/api.php/usuarios/${userId}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Perfil actualizado con éxito');
                        location.reload();
                    } else {
                        alert('Error al actualizar el perfil: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error en la conexión: ' + error.message);
                });
            });
        }
    } else if (followButton) {
        followButton.addEventListener('click', function() {
            const action = isFollowing ? 'unfollow' : 'follow';
            fetch(`../API_FOLLOW/${action}_user.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    seguidor: userId,
                    seguido: profileId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    isFollowing = !isFollowing;
                    followButton.textContent = isFollowing ? 'Dejar de seguir' : 'Seguir';
                    followButton.classList.toggle('following');
                    // Actualizar el contador de seguidores
                    followersCount.textContent = parseInt(followersCount.textContent) + (isFollowing ? 1 : -1);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estado de seguimiento');
            });
        });
    }

    // Fetch followers count
    fetch(`../API_SEGUIDORES/get_followers.php?id_usuario=${profileId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                followersCount.textContent = data.followers.length;
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error al obtener seguidores:', error);
            followersCount.textContent = 'Error';
        });

    // Fetch following count
    fetch(`../API_SEGUIDORES/get_following.php?id_usuario=${profileId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                followingCount.textContent = data.following.length;
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error al obtener seguidos:', error);
            followingCount.textContent = 'Error';
        });
});