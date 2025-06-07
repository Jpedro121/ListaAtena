// Toggle para mostrar/esconder senha
document.querySelectorAll('.password-toggle').forEach(toggle => {
  toggle.addEventListener('click', function() {
    const targetId = this.getAttribute('data-target');
    const target = document.getElementById(targetId);
    this.classList.toggle('bi-eye-slash');
    this.classList.toggle('bi-eye');
    target.type = target.type === 'password' ? 'text' : 'password';
  });
});

// Preview da imagem de perfil
document.getElementById('avatarUpload')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (event) => {
      document.getElementById('avatarPreview').src = event.target.result;
    };
    reader.readAsDataURL(file);
  }
});

// Enviar foto de perfil (AJAX)
document.getElementById('saveAvatarBtn')?.addEventListener('click', async () => {
  const formData = new FormData(document.getElementById('avatarForm'));
  
  try {
    const response = await fetch('upload_avatar.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();
    
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Erro ao atualizar a foto');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Erro ao enviar o arquivo');
  }
});

// Validação de senha
document.querySelector('form[method="POST"]')?.addEventListener('submit', (e) => {
  const newPassword = document.getElementById('newPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  
  if (newPassword !== confirmPassword) {
    e.preventDefault();
    alert('As senhas não coincidem!');
    return;
  }
  
  if (newPassword.length < 8) {
    e.preventDefault();
    alert('A senha deve ter pelo menos 8 caracteres');
  }
});