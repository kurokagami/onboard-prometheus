// <--- JSON ---> //

// Carregar e processar o arquivo JSON
document.addEventListener('DOMContentLoaded', async function () {
  try {
    // Carrega o arquivo JSON e espera pela resolução da promessa
    const response = await fetch('public/json/date_port.json');
    const data = await response.json();

    // Cria botões e adiciona eventos de clique
    createButtons(data.projects);
    addClickEventsToButtons(data.projects);
  } catch (error) {
    console.error('Erro ao carregar o arquivo JSON:', error);
  }
});

// Função para criar botões dinamicamente
function createButtons(projects) {
  const buttonContainer = document.getElementById('buttons');
  buttonContainer.innerHTML = ''; // Limpa os botões existentes

  projects.forEach((project, index) => {
    const button = document.createElement('button');
    button.classList.add('each-button'); // Adiciona a classe CSS
    button.id = `project-${project.id}`;
    button.textContent = `Projeto ${index + 1}`;
    buttonContainer.appendChild(button);
  });
}

// Função para adicionar eventos de clique aos botões
function addClickEventsToButtons(projects) {
  projects.forEach(project => {
    const button = document.getElementById(`project-${project.id}`);
    if (button) {
      button.addEventListener('click', () => {
        showProjectInfo(project);
      });
    }
  });
}

// Função para exibir as informações do projeto
function showProjectInfo(project) {
  //Pega a div do projeto no html/php
  const projectInfoContainer = document.getElementById('project-info-container');
  projectInfoContainer.innerHTML = ''; // Limpa o conteúdo anterior

  //Cria as divisões, classes e etc.
  const title = document.createElement('div'); //Cria uma div
  title.classList.add('title'); // Adiciona uma classe à div
  title.textContent = project.title; //Joga o titulo do json na div

  //O mesmo acontece com as outras informações do json
  const imageDiv = document.createElement('div');
  imageDiv.classList.add('image');

  const image = document.createElement('img');
  image.classList.add('project-image');
  image.src = project.image;
  image.alt = project.title;

  const description = document.createElement('div');
  description.classList.add('description');
  description.textContent = project.description;

  //Criar a variavel do link do projeto
  const link = document.createElement('a');
  link.classList.add('link');

  //Lógica para adicionar o link
  //Se projeto.link conter algo, criar href com o link. Senão, Apenas jogar texto.
  if (project.link != "") {
    link.href = project.link;
    link.textContent = "Disponivel Aqui";
  } else {
    link.textContent = "Ainda não Disponivel";
  }

  // Adiciona os elementos diretamente ao projectInfoContainer
  projectInfoContainer.appendChild(title);
  projectInfoContainer.appendChild(imageDiv);
  imageDiv.appendChild(image);
  projectInfoContainer.appendChild(description);
  projectInfoContainer.appendChild(link);

  // Exibe as informações do projeto
  projectInfoContainer.style.display = 'block';
}

