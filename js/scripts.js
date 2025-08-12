document.addEventListener('DOMContentLoaded', () => {
    const secciones = ['inicio', 'referenciacion', 'asignacion', 'seguimiento', 'contacto'];
    const enlaces = document.querySelectorAll('nav ul li a');

    enlaces.forEach(enlace => {
        enlace.addEventListener('click', (e) => {
            e.preventDefault();
            const seccionActiva = enlace.getAttribute('data-seccion');

            secciones.forEach(sec => {
                document.getElementById(sec).style.display = (sec === seccionActiva) ? 'block' : 'none';
            });
        });
    });
});
