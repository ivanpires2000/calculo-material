<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Materiais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        #parede-espera {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            display: none; /* Inicialmente escondido */
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 1000;
        }

        #parede-espera img {
            width: 80px;
            margin-bottom: 10px;
        }

        #parede-espera p {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .invisible {
            display: none !important;
        }
    </style>
</head>
<body>
    <main>
        <h1 class="text-center mt-md-2">Calculadora de Materiais</h1>

        <div class="container">
            <div class="row g-2">
                <fieldset class="row g-2">
                    <legend>Comodo</legend>
                    <div class="col-md-6">
                        <label for="comodo-largura" class="form-label">Largura (m)</label>
                        <input type="number" class="form-control" id="comodo-largura" required>
                        <span id="comodo-largura-validacao" class="text-danger invisible"></span>
                    </div>
                    <div class="col-md-6">
                        <label for="comodo-comprimento" class="form-label">Comprimento (m)</label>
                        <input type="number" class="form-control" id="comodo-comprimento" required>
                        <span id="comodo-comprimento-validacao" class="text-danger invisible"></span>
                    </div>
                </fieldset>
                <fieldset class="row g-2">
                    <legend>Piso</legend>
                    <div class="col-md-6">
                        <label for="piso-largura" class="form-label">Largura (m)</label>
                        <input type="number" class="form-control" id="piso-largura" required>
                        <span id="piso-largura-validacao" class="text-danger invisible"></span>
                    </div>
                    <div class="col-md-6">
                        <label for="piso-comprimento" class="form-label">Comprimento (m)</label>
                        <input type="number" class="form-control" id="piso-comprimento" required>
                        <span id="piso-comprimento-validacao" class="text-danger invisible"></span>
                    </div>
                </fieldset>
                <div class="col-md-12">
                    <label for="margem" class="form-label">Margem (%)</label>
                    <input type="number" class="form-control" id="margem" required>
                    <span id="margem-validacao" class="text-danger invisible"></span>
                </div>
                <div class="col-md-12">
                    <button class="btn btn-primary" id="btn-calcular" onclick="processar();">Calcular</button>
                </div>
                <div class="col-md-12">
                    <div id="resultado"></div>
                </div>
            </div>
        </div>

        <div id="parede-espera">
            <img src="images/carregando.gif" alt="Carregando...">
            <p>Por favor, aguarde...</p>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function toggleLoading() {
            const div = document.getElementById("parede-espera");
            div.style.display = div.style.display === "none" ? "flex" : "none";
        }

        function processar() {
            try {
                toggleLoading(); // Exibe o modal de carregamento

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

                const dados = JSON.stringify(medidas);

                fetch('/calculo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: dados,
                })
                    .then((resposta) => resposta.json())
                    .then((resultado) => {
                        toggleLoading(); // Oculta o modal após o processamento

                        const elementoResultado = document.getElementById("resultado");

                        if (resultado.erro) {
                            resultado.erro.forEach((erroMsg) => {
                                exibirErro(erroMsg.idCampo, erroMsg.mensagem);
                            });
                            return;
                        }

                        const exibir =
                            `<p>Área do comodo: ${resultado.areaComodo} m²</p>` +
                            `<p>Área do piso: ${resultado.areaPiso} m²</p>` +
                            `<p>Quantidade de pisos: ${resultado.quantidade}</p>` +
                            `<p>Quantidade com margem: ${resultado.quantidadeMargem}</p>` +
                            `<p>Total a ser comprado: ${resultado.quantidadeTotal}</p>`;

                        elementoResultado.innerHTML = exibir;
                    })
                    .catch((erro) => {
                        toggleLoading(); // Oculta o modal mesmo em caso de erro
                        alert("Ocorreu um erro durante o cálculo.");
                        console.error(erro);
                    });
            } catch (e) {
                toggleLoading(); // Garante que o modal seja ocultado em caso de erro
                alert("Erro inesperado ao processar a solicitação.");
                console.error(e);
            }
        }

        function exibirErro(idElemento, mensagemErro) {
            const spanId = idElemento + "-validacao";
            const input = document.getElementById(idElemento);
            const spanErro = document.getElementById(spanId);

            spanErro.innerHTML = mensagemErro;
            spanErro.classList.remove("invisible");
            input.classList.add("border", "border-danger-subtle");
        }
    </script>
</body>
</html>
