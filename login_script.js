/* Funcion para ocultar/ver contraseña (login.php)*/


document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('contrasena');
    const togglePasswordIcon = document.getElementById('togglePassword');

    togglePasswordIcon.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Cambia la clase del icono para mostrar el "ojo abierto"
            togglePasswordIcon.classList.remove('bi-eye-slash');
            togglePasswordIcon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            // Cambia la clase del icono para mostrar el "ojo tachado"
            togglePasswordIcon.classList.remove('bi-eye');
            togglePasswordIcon.classList.add('bi-eye-slash');
        }
    });
});