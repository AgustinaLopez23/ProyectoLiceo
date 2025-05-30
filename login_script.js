document.addEventListener('DOMContentLoaded', function() {
  const passwordInput = document.getElementById('contrasena');
  const togglePasswordIcon = document.getElementById('togglePassword');
  const iconEye = document.getElementById('icon-eye');
  const iconEyeOff = document.getElementById('icon-eye-off');

  if (togglePasswordIcon && passwordInput && iconEye && iconEyeOff) {
    togglePasswordIcon.addEventListener('click', function() {
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        iconEye.style.display = 'inline';        // OJO ABIERTO visible (contraseña visible)
        iconEyeOff.style.display = 'none';       // OJO CERRADO oculto
      } else {
        passwordInput.type = 'password';
        iconEye.style.display = 'none';          // OJO ABIERTO oculto
        iconEyeOff.style.display = 'inline';     // OJO CERRADO visible (contraseña oculta)
      }
    });

    // Accesibilidad: alternar con Enter o Espacio
    togglePasswordIcon.addEventListener('keydown', function(e) {
      if (e.key === " " || e.key === "Enter") {
        togglePasswordIcon.click();
        e.preventDefault();
      }
    });
  }
});