<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Portifólio</title>
    <link rel="stylesheet" href="./data/cascading-port.css">
</head>

<body>
    <?php include_once('./data/header.phtml') ?>
    <nav>
        <!-- Adiciona a função apropriada ao botão Home com base na página atual -->
        <button onclick="redirecionarInd()"><b>Home</b></button>
        <button onclick="dynamicDisplayDiv('odd')" class="<?php echo ($current_file === 'projetos.php') ? 'hidden' : ''; ?>"><b>Contate-Me</b></button>
        <button onclick="redirecionarProj()"><b>Meus Projetos</b></button>
    </nav>
    <div id="box">
        <div id="box-content">
            <div class="content">
                <div id="left">
                    <div class="hello">
                        <h1>Bem vindo a Página de Projetos</h1>
                    </div>
                    <div id="photo-div">
                        <img id="photo-project" class="circle" src="./img/robots/work.jpg" alt="robot-work">
                    </div>
                    <div id="text-proj">
                        <h2>Aqui você poderá encontrar meus projetos ou ideias.</h2>
                        <h2>Alguns ainda não foram iniciados, outros estão em andamento e outros pausados por problemas técnicos.</h2>
                        <h2>O projeto ficará disponivel para ser acessado assim que uma versão de teste ou superior for lançada.</h2>
                        <h2>Você também pode acompanhar o andamento de cada um deles em meu twitter ou instagram, ou até mesmo disponibilizo alguns em meu github.</h2>
                        <h2>Todas as medias podem ser acessadas na pagina home.</h2>
                    </div>
                </div>
                <div id="right">
                    <div class="hello">
                        <h1>Projetos</h1>
                    </div>
                    <div id="buttons">
                    </div>
                    <div id="project-info-container">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once('./data/footer.phtml') ?>
    <script src="./data/script-proj.js"></script>
    <script src="./data/general-script.js"></script>
</body>

</html>