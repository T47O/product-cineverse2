function listPost() {
    const postId = document.getElementById("postId").value;
    if (postId) {
        window.location.href = `manage_post.php?action=list&id=${postId}`;
    } else {
        alert("Por favor, introduce un ID de publicación.");
    }
}

function deletePost() {
    const postId = document.getElementById("postId").value;
    if (postId) {
        window.location.href = `manage_post.php?action=delete&id=${postId}`;
    } else {
        alert("Por favor, introduce un ID de publicación.");
    }
}

function listUser() {
    const userId = document.getElementById("userId").value;
    if (userId) {
        window.location.href = `manage_user.php?action=list&id=${userId}`;
    } else {
        alert("Por favor, introduce un ID de usuario.");
    }
}

function deleteUser() {
    const userId = document.getElementById("userId").value;
    if (userId) {
        window.location.href = `manage_user.php?action=delete&id=${userId}`;
    } else {
        alert("Por favor, introduce un ID de usuario.");
    }
}
