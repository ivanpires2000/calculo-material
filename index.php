<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Materiais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        main {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 40px auto;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            border-radius: 5px;
            box-shadow: inset 0px 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .btn {
            border-radius: 5px;
            padding: 10px 20px;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            display: none;
        }

        .input-container {
            margin-bottom: 15px;
        }

        #resultado {
            margin-top: 20px;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
        }

        #parede-espera {
            width: 100%;
            height: 100%;
            z-index: 1000;
            background: #c3c3c3;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0.5;
            display: none;
        }
    </style>
</head>
<body>
    <main>
        <h1 class="text-center">Calculadora de Materiais</h1>
        <div class="container">
            <div class="row g-2">
                <fieldset class="row g-2">
                    <legend>Comôdo</legend>
                    <div class="col-md-6 input-container">
                        <label for="comodo-largura" class="form-label">Largura (m)</label>
                        <input type="number" class="form-control" id="comodo-largura" required>
                        <span id="comodo-largura-validacao" class="error-message">A largura é obrigatória.</span>
                    </div>
                    <div class="col-md-6 input-container">
                        <label for="comodo-comprimento" class="form-label">Comprimento (m)</label>
                        <input type="number" class="form-control" id="comodo-comprimento" required>
                        <span id="comodo-comprimento-validacao" class="error-message">O comprimento é obrigatório.</span>
                    </div>
                </fieldset>

                <fieldset class="row g-2">
                    <legend>Piso</legend>
                    <div class="col-md-6 input-container">
                        <label for="piso-largura" class="form-label">Largura (m)</label>
                        <input type="number" class="form-control" id="piso-largura" required>
                        <span id="piso-largura-validacao" class="error-message">A largura do piso é obrigatória.</span>
                    </div>
                    <div class="col-md-6 input-container">
                        <label for="piso-comprimento" class="form-label">Comprimento (m)</label>
                        <input type="number" class="form-control" id="piso-comprimento" required>
                        <span id="piso-comprimento-validacao" class="error-message">O comprimento do piso é obrigatório.</span>
                    </div>
                </fieldset>

                <div class="col-md-12 input-container">
                    <label for="margem" class="form-label">Margem (%)</label>
                    <input type="number" class="form-control" id="margem" required>
                    <span id="margem-validacao" class="error-message">A margem é obrigatória.</span>
                </div>

                <div class="col-md-12">
                    <button class="btn btn-primary" id="btn-calcular" onclick="processar();">Calcular</button>
                </div>

                <div class="col-md-12">
                    <div id="resultado"></div>
                </div>
            </div>
        </div>
        <div id="parede-espera" class="opacity-50">
            <img src="images/carregando.gif" alt="Carregando">
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        realizarBinds();

        function realizarBinds() {
            const comodoLargura = document.getElementById("comodo-largura");
            const comodoComprimento = document.getElementById("comodo-comprimento");
            const pisoLargura = document.getElementById("piso-largura");
            const pisoComprimento = document.getElementById("piso-comprimento");
            const margem = document.getElementById("margem");

            comodoLargura.addEventListener("focus", removerMensagemErro);
            comodoComprimento.addEventListener("focus", removerMensagemErro);
            pisoLargura.addEventListener("focus", removerMensagemErro);
            pisoComprimento.addEventListener("focus", removerMensagemErro);
            margem.addEventListener("focus", removerMensagemErro);
        }

        function processar() {
            try {
                const comodoLargura = document.getElementById("comodo-largura").value;
                const comodoComprimento = document.getElementById("comodo-comprimento").value;
                const pisoLargura = document.getElementById("piso-largura").value;
                const pisoComprimento = document.getElementById("piso-comprimento").value;
                const margem = document.getElementById("margem").value;

                const medidas = { comodoLargura, comodoComprimento, pisoLargura, pisoComprimento, margem };
                const dados = JSON.stringify(medidas);

                document.getElementById("parede-espera").style.display = "block"; // Show overlay

                fetch('/calculo.php', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: dados
                })
                .then(resposta => resposta.json())
                .then(resultado => {
                    document.getElementById("parede-espera").style.display = "none"; // Hide overlay
                    let elementoResultado = document.getElementById("resultado");

                    if (resultado.erro) {
                        resultado.erro.forEach(erroMsg => {
                            exibirErro(erroMsg.idCampo, erroMsg.mensagem);
                        });
                        return;
                    }

                    const exibir =
                        "<p>Área do cômodo: " + resultado.areaComodo + " m²</p>" +
                        "<p>Área do piso: " + resultado.areaPiso + " m²</p>" +
                        "<p>Quantidade de pisos: " + resultado.quantidade + "</p>" +
                        "<p>Quantidade para margem: " + resultado.quantidadeMargem + "</p>" +
                        "<p>Total a ser comprado: " + resultado.quantidadeTotal + "</p>";

                    elementoResultado.innerHTML = exibir;
                })
                .catch(erro => {
                    alert("Ocorreu um erro");
                    console.error(erro);
                });
            } catch (e) {
                alert("Ocorreu um erro ao atender a sua solicitação.");
                console.error("Erro:", e);
            }
        }

        function exibirErro(idElemento, mensagemErro) {
            const spanId = idElemento + "-validacao";
            const input = document.getElementById(idElemento);
            const spanErro = document.getElementById(spanId);

            spanErro.innerHTML = mensagemErro;
            spanErro.style.display = 'block';
            input.classList.add("border", "border-danger");
        }

        function removerMensagemErro(e) {
            const spanId = e.srcElement.id + "-validacao";
            const input = e.srcElement;
            const spanErro = document.getElementById(spanId);

            spanErro.innerHTML = "";
            spanErro.style.display = 'none';
            input.classList.remove("border", "border-danger");
        }
    </script>
</body>
</html>
