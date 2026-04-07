    </main>
<script>
// Auto-inject CSRF token into all POST forms
(function () {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    if (!token) return;
    document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function (form) {
        if (form.querySelector('input[name="_csrf"]')) return;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_csrf';
        input.value = token;
        form.prepend(input);
    });
})();
</script>
</body>
</html>
