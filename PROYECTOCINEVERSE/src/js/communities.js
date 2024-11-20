// Global variables
let isAdmin = false;
let userId, communityId;

// DOM Content Loaded Event Listener
document.addEventListener('DOMContentLoaded', () => {
  userId = document.body.dataset.userId;
  loadCommunities();
  setupEventListeners();
});

// Setup Event Listeners
function setupEventListeners() {
  const createCommunityBtn = document.getElementById('createCommunityBtn');
  const createCommunityForm = document.getElementById('createCommunityForm');
  const editCommunityForm = document.getElementById('editCommunityForm');

  if (createCommunityBtn) {
    createCommunityBtn.addEventListener('click', () => {
      document.getElementById('createCommunityModal').classList.add('active');
    });
  }

  if (createCommunityForm) {
    createCommunityForm.addEventListener('submit', createCommunity);
  }

  if (editCommunityForm) {
    editCommunityForm.addEventListener('submit', editCommunity);
  }

  // Chat-specific event listeners
  const messageForm = document.getElementById('messageForm');
  if (messageForm) {
    messageForm.addEventListener('submit', sendMessage);
    communityId = new URLSearchParams(window.location.search).get('community_id');
    loadMembers();
    loadMessages();
    setInterval(loadMessages, 5000);
  }
}

// Load Communities
async function loadCommunities() {
  try {
    const response = await fetch(`../API_COMUNIDAD/get_user_communities.php?user_id=${userId}`);
    const data = await response.json();
    
    renderCommunities('member-communities', data.member_communities, true);
    renderCommunities('non-member-communities', data.non_member_communities, false);
  } catch (error) {
    console.error('Error:', error);
  }
}

// Render Communities
function renderCommunities(containerId, communities, isMember) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = '';

  communities.forEach(community => {
    const card = document.createElement('div');
    card.className = 'post';
    card.innerHTML = `
      <div class="post-content">
        <h3 class="post-title">${community.Titulo}</h3>
        <p class="post-description">${community.Descripcion}</p>
        <div class="post-actions">
          ${isMember ? 
            `<button class="settings-button" onclick="window.location.href='chat.php?community_id=${community.ID_comunidad}'">
              Chatear
            </button>` :
            `<button class="settings-button" onclick="joinCommunity(${community.ID_comunidad})">
              Unirse a la comunidad
            </button>`
          }
          ${community.ID_Administrador == userId ?
            `<button class="settings-button" onclick="openEditModal(${community.ID_comunidad}, '${community.Titulo}', '${community.Descripcion}')">
              Editar
            </button>` :
            (isMember ? 
              `<button class="settings-button" onclick="leaveCommunity(${community.ID_comunidad})">
                Salir de la comunidad
              </button>` : 
              ''
            )
          }
        </div>
      </div>
    `;
    container.appendChild(card);
  });
}

// Join Community
async function joinCommunity(communityId) {
  try {
    const response = await fetch('../API_COMUNIDAD/join_community.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id_usuario: userId,
        id_comunidad: communityId
      })
    });
    const data = await response.json();
    if (data.success) {
      loadCommunities();
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Leave Community
async function leaveCommunity(communityId) {
  try {
    const response = await fetch('../API_COMUNIDAD/leave_community.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id_usuario: userId,
        id_comunidad: communityId
      })
    });
    const data = await response.json();
    if (data.success) {
      loadCommunities();
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Open Edit Modal
function openEditModal(communityId, title, description) {
  document.getElementById('communityId').value = communityId;
  document.getElementById('editTitle').value = title;
  document.getElementById('editDescription').value = description;
  document.getElementById('editModal').classList.add('active');
}

// Close Edit Modal
function closeEditModal() {
  document.getElementById('editModal').classList.remove('active');
}

// Edit Community
async function editCommunity(e) {
  e.preventDefault();
  const formData = {
    id_comunidad: document.getElementById('communityId').value,
    titulo: document.getElementById('editTitle').value,
    descripcion: document.getElementById('editDescription').value
  };

  try {
    const response = await fetch('../API_COMUNIDAD/update_community.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formData)
    });
    const data = await response.json();
    if (data.success) {
      closeEditModal();
      loadCommunities();
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Close Create Modal
function closeCreateModal() {
  document.getElementById('createCommunityModal').classList.remove('active');
}

// Create Community
async function createCommunity(e) {
  e.preventDefault();
  const title = document.getElementById('communityTitle').value;
  const description = document.getElementById('communityDescription').value;

  try {
    const response = await fetch('../API_COMUNIDAD/create_community.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        titulo: title,
        descripcion: description,
        id_administrador: userId
      })
    });
    const data = await response.json();
    if (data.success) {
      alert('Comunidad creada exitosamente!');
      closeCreateModal();
      loadCommunities();
    } else {
      alert('Error al crear la comunidad: ' + data.message);
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Ocurrió un error al crear la comunidad');
  }
}

// Load Members (for chat)
async function loadMembers() {
  try {
    const response = await fetch(`../API_COMUNIDAD/list_community_members.php?id_comunidad=${communityId}`);
    if (!response.ok) throw new Error('Network response was not ok');
    const data = await response.json();
    if (data.success) {
      isAdmin = data.members.some(member => member.ID_Usuario == userId && member.rol === 'admin');
      renderMembers(data.members);
    } else {
      console.error('Error loading members:', data.message);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Render Members (for chat)
function renderMembers(members) {
  const memberList = document.getElementById('memberList');
  if (!memberList) return;

  memberList.innerHTML = '<h3 style="color: #fff; margin-bottom: 1rem;">Miembros</h3>';
  
  if (!Array.isArray(members)) {
    console.error('Members is not an array:', members);
    return;
  }

  members.forEach(member => {
    const memberElement = document.createElement('div');
    memberElement.className = 'member';
    memberElement.innerHTML = `
      <div>
        <span style="color: #fff;">${member.Nombre}</span>
        <span style="color: #999;">@${member.ID_Usuario}</span>
        ${member.rol === 'admin' ? '<span style="color: #e50914;"> (Admin)</span>' : ''}
      </div>
      ${isAdmin && member.rol !== 'admin' && member.ID_Usuario != userId ? 
        `<button onclick="expelMember(${member.ID_Usuario})">Expulsar</button>` : 
        ''}
    `;
    memberList.appendChild(memberElement);
  });

  console.log('isAdmin:', isAdmin);
}

// Load Messages (for chat)
async function loadMessages() {
  try {
    const response = await fetch(`../API_COMUNIDAD/get_community_message.php?community_id=${communityId}`);
    if (!response.ok) throw new Error('Network response was not ok');
    const text = await response.text();
    try {
      const data = JSON.parse(text);
      if (data.success) {
        renderMessages(data.messages);
      }
    } catch (e) {
      console.error('Invalid JSON:', text);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Render Messages (for chat)
function renderMessages(messages) {
  const messageList = document.getElementById('messageList');
  if (!messageList) return;

  messageList.innerHTML = '';
  
  if (!Array.isArray(messages)) {
    console.error('Messages is not an array:', messages);
    return;
  }

  messages.forEach(message => {
    const messageElement = document.createElement('div');
    messageElement.className = `message ${message.ID_Usuario == userId ? 'current-user' : ''}`;
    messageElement.innerHTML = `
      <div class="message-sender">${message.Usuario} @${message.ID_Usuario}</div>
      <div class="message-content">${message.Contenido}</div>
    `;
    messageList.appendChild(messageElement);
  });
  messageList.scrollTop = messageList.scrollHeight;
}

// Send Message (for chat)
async function sendMessage(e) {
  e.preventDefault();
  const messageInput = document.getElementById('messageInput');
  const content = messageInput.value.trim();
  
  if (!content) return;

  try {
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('community_id', communityId);
    formData.append('content', content);

    const response = await fetch('../API_COMUNIDAD/send_message_to_community.php', {
      method: 'POST',
      body: formData
    });

    if (!response.ok) throw new Error('Network response was not ok');
    const text = await response.text();
    try {
      const data = JSON.parse(text);
      if (data.success) {
        messageInput.value = '';
        loadMessages();
      } else {
        console.error('Error sending message:', data.message);
      }
    } catch (e) {
      console.error('Invalid JSON:', text);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Expel Member (for chat)
async function expelMember(memberId) {
  if (confirm('¿Estás seguro de que quieres expulsar a este miembro?')) {
    try {
      const response = await fetch('../API_COMUNIDAD/leave_community.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          id_usuario: memberId,
          id_comunidad: communityId
        })
      });
      const data = await response.json();
      if (data.success) {
        loadMembers();
        alert('El miembro ha sido expulsado de la comunidad.');
      } else {
        alert('Error al expulsar al miembro: ' + data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error al expulsar al miembro.');
    }
  }
}