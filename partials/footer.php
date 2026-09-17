  </main>
</div>
<script>
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });
  document.querySelectorAll('.auto-submit').forEach(el => el.addEventListener('change', () => el.form.submit()));
</script>
</body>
</html>
