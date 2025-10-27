</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// cerrar sidebar al hacer clic fuera (móvil)
document.addEventListener('click', function(e){
  var sb = document.getElementById('sidebar');
  if(!sb || !sb.classList.contains('open')) return;
  if(!sb.contains(e.target) && !e.target.closest('.toggle-btn')) sb.classList.remove('open');
});
</script>
</body>
</html>
