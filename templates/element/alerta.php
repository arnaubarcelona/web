<div id="custom-alert" class="message alert hidden">
    <span id="custom-alert-text"></span>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertDiv = document.getElementById('custom-alert');
    const alertText = document.getElementById('custom-alert-text');

    // Funció per mostrar l'alerta
    window.mostraAlerta = function (missatge) {
        if (alertText && alertDiv) {
            alertText.innerHTML = missatge;
            alertDiv.classList.remove('hidden');
            alertDiv.style.display = 'flex';
        }
    };

    // Amagar el div en fer clic
    if (alertDiv) {
        alertDiv.addEventListener('click', function () {
            alertDiv.classList.add('hidden');
            alertDiv.style.display = 'none';
        });
    }
});
</script>
