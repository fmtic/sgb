document.addEventListener('DOMContentLoaded', function () {
    function aplicarMascaraTelefone(campo) {
        campo.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '').slice(0, 11); // Apenas números, no máximo 11

            if (valor.length <= 10) {
                // Formato para telefone fixo: (99) 9999-9999
                valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else {
                // Formato para celular: (99) 99999-9999
                valor = valor.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            }

            this.value = valor;
        });
    }

    ['telefone', 'telefone2'].forEach(function (id) {
        const campo = document.getElementById(id);
        if (campo) aplicarMascaraTelefone(campo);
    });
});
