document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formConversor');
    const convertedDiv = document.getElementById('converted');
    const submitButton = form.querySelector('input[type="submit"]');
    const amountInput = document.getElementById('amount');
    const fromSelect = document.getElementById('from');
    const toSelect = document.getElementById('to');

    // Função para controlar o estado de carregamento
    const setLoadingState = (loading) => {
        submitButton.disabled = loading;
        submitButton.value = loading ? 'Convertendo...' : 'Converter';
    };

    // Formata número
    const formatarNumero = (n, d = 2) =>
        parseFloat(n).toFixed(d).replace('.', ',');

    // Limpa o resultado
    const limparResultado = () => (convertedDiv.innerHTML = '');

    // Permite apenas números, vírgulas e pontos
    amountInput.addEventListener('keypress', (e) => {
        if (!/[0-9,.]/.test(String.fromCharCode(e.which))) e.preventDefault();
    });

    // Limpa quando houver alteração
    [amountInput, fromSelect, toSelect].forEach((el) =>
        el.addEventListener('input', limparResultado)
    );

    // Envia o formulário via AJAX
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const amount = parseFloat(amountInput.value);
        const from = fromSelect.value;
        const to = toSelect.value;

        if (!amount || !from || !to || from === to || amount <= 0) {
            limparResultado();
            return;
        }

        setLoadingState(true);
        limparResultado();

        try {
            const response = await fetch('/convert', {
                method: 'POST',
                body: new FormData(form)
            });

            const data = await response.json();

            if (data.success) {
                convertedDiv.innerHTML = `
                    <span> $ ${formatarNumero(data.result)} </span>
                `;
            } else {
                convertedDiv.textContent = 'Erro na conversão.';
            }
        } catch {
            convertedDiv.textContent = 'Erro de conexão.';
        } finally {
            setLoadingState(false);
        }
    });
});
