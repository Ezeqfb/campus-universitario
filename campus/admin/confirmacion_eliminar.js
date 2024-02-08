document.addEventListener("DOMContentLoaded", function() {
    //En "admin_curso.php" usamos class="eliminar-link"
    var eliminarCurso = document.querySelectorAll(".eliminar-link");

    eliminarCurso.forEach(function(link) {
        link.addEventListener("click", function(event) {
            event.preventDefault();

            if (window.confirm("¿Estás seguro de que deseas eliminar este elemento?")) {
                //Redirigir a "procesar_eliminacion_curso.php"
                window.location.href = this.getAttribute("href");
            }
        });
    });
});