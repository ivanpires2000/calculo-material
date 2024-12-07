<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Materiais</title>
    <!-- Bootstrap para estilização e layout responsivo -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Estilização do modal "Aguarde" */
        #parede-espera {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 1000;
        }

        #parede-espera img {
            width: 80px;
            margin-bottom: 15px;
        }

        #parede-espera p {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Estilização para campos de erro */
        .error {
            border-color: #dc3545;
            background-color: #f8d7da;
        }

        .validacao-msg {
            color: #dc3545;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <main class="container mt-5">
        <h1 class="text-center mb-4">Calculadora de Materiais</h1>

        <!-- Formulário -->
        <form id="calculadora-form">
            <div class="row g-4">
                <!-- Dimensões do cômodo -->
                <fieldset class="col-md-6">
                    <legend>Cômodo</legend>
                    <div class="mb-3">
                        <label for="comodo-largura" class="form-label">Largura (m)</label>
                        <input type="number" class="form-control" id="comodo-largura" required>
                        <div id="comodo-largura-validacao" class="validacao-msg invisible"></div>
                    </div>
                    <div class="mb-3">
                        <label for="comodo-comprimento" class="form-label">Comprimento (m)</label>
                        <input type="number" class="form-control" id="comodo-comprimento" required>
                        <div id="comodo-comprimento-validacao" class="validacao-msg invisible"></div>
                    </div>
                </fieldset>

                <!-- Dimensões do piso -->
                <fieldset class="col-md-6">
                    <legend>Piso</legend>
                    <div class="mb-3">
                        <label for="piso-largura" class="form-label">Largura (m)</label>
                        <input type="number" class="form-control" id="piso-largura" required>
                        <div id="piso-largura-validacao" class="validacao-msg invisible"></div>
                    </div>
                    <div class="mb-3">
                        <label for="piso-comprimento" class="form-label">Comprimento (m)</label>
                        <input type="number" class="form-control" id="piso-comprimento" required>
                        <div id="piso-comprimento-validacao" class="validacao-msg invisible"></div>
                    </div>
                </fieldset>

                <!-- Margem percentual -->
                <div class="col-md-12 mb-3">
                    <label for="margem" class="form-label">Margem (%)</label>
                    <input type="number" class="form-control" id="margem" required>
                    <div id="margem-validacao" class="validacao-msg invisible"></div>
                </div>

                <!-- Botão de cálculo -->
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-primary" id="btn-calcular" onclick="processar();">Calcular</button>
                </div>
            </div>

            <!-- Div para exibição do resultado -->
            <div id="resultado" class="mt-4"></div>
        </form>

        <!-- Modal de carregamento -->
        <div id="parede-espera">
            <img src="images/carregando.gif" alt="Carregando...">
            <p>Por favor, aguarde...</p>
        </div>
    </main>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Função para controlar o modal de carregamento
        function toggleLoading(show) {
            const div = document.getElementById("parede-espera");
            div.style.display = show ? "flex" : "none";
        }

        // Função principal para processamento do cálculo
        function processar() {
            const resultadoDiv = document.getElementById("resultado");
            resultadoDiv.innerHTML = ""; // Limpa resultados anteriores

            toggleLoading(true); // Exibe o modal de carregamento

            // Recupera os valores dos campos de entrada
            const comodoLargura = document.getElementById("comodo-largura").value;
            const comodoComprimento = document.getElementById("comodo-comprimento").value;
            const pisoLargura = document.getElementById("piso-largura").value;
            const pisoComprimento = document.getElementById("piso-comprimento").value;
            const margem = document.getElementById("margem").value;

            const medidas = {
                comodoLargura,
                comodoComprimento,
                pisoLargura,
                pisoComprimento,
                margem,
            };

            // Envia dados ao servidor usando Fetch API
            fetch('/calculo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(medidas),
            })
            .then((resposta) => resposta.json())
            .then((resultado) => {
                toggleLoading(false); // Esconde o modal de carregamento

                if (resultado.erro) {
                    // Exibe mensagens de erro nos campos correspondentes
                    resultado.erro.forEach((erroMsg) => {
                        exibirErro(erroMsg.idCampo, erroMsg.mensagem);
                    });
                    return;
                }

                // Exibe os resultados do cálculo
                const exibir =
                    `<p><strong>Área do Cômodo:</strong> ${resultado.areaComodo} m²</p>` +
                    `<p><strong>Área do Piso:</strong> ${resultado.areaPiso} m²</p>` +
                    `<p><strong>Quantidade de Pisos:</strong> ${resultado.quantidade}</p>` +
                    `<p><strong>Quantidade com Margem:</strong> ${resultado.quantidadeMargem}</p>` +
                    `<p><strong>Total a ser Comprado:</strong> ${resultado.quantidadeTotal}</p>`;

                resultadoDiv.innerHTML = exibir;
            })
            .catch((erro) => {
                toggleLoading(false); // Esconde o modal de carregamento
                alert("Ocorreu um erro durante o cálculo.");
                console.error(erro);
            });
        }

        // Exibe mensagens de erro para validação
        function exibirErro(idElemento, mensagemErro) {
            const spanId = idElemento + "-validacao";
            const input = document.getElementById(idElemento);
            const spanErro = document.getElementById(spanId);

            spanErro.innerHTML = mensagemErro;
            spanErro.classList.remove("invisible");
            input.classList.add("error"); // Aplica a classe de erro
        }
    </script>
</body>
</html>
