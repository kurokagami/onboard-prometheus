// <--- Scripts do Formulário ---> //

// Adiciona DOM evento para o JSON
document.addEventListener('DOMContentLoaded', async function () {
  try {
    // Carregar e processar o arquivo JSON das Medias
    const response = await fetch('./data/projetos.json');
    const data = await response.json();

    // Criar os links de redes sociais
    createMedias(data.social_medias);
  } catch (error) {
    console.error('Erro ao carregar o arquivo JSON:', error);
  }
});

// Adiciona DOM evento para o Fomulário//
document.addEventListener('DOMContentLoaded', async function () {

  const contactForm = document.querySelector('form[name="contactForm"]');

  // Regex //
  const emailRegex = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9.-]+$/;
  const phoneRegex = /^(\([0-9]{2}\) [0-9]{4,5}-[0-9]{4})?$/;

  contactForm.addEventListener('submit', function () {
    // Função global para validação do formulário

    // Função de validação dos campos do formulário

    // Pega os valores dos campos do formulário
    let x = document.forms["contactForm"]["name"].value.trim(); // Trim para remover espaços extras no final e inicio
    let y = document.forms["contactForm"]["email"].value.trim().toLowerCase(); //Garante email sempre minúsculo
    let z = document.forms["contactForm"]["message"].value.trim();
    let a = document.forms["contactForm"]["phone"].value.trim();
    let b = document.forms["contactForm"]["ddd"].value;

    // Check Nome e Mensagem
    if (x === "" || z === "") {
      alert("Preencha todos os campos obrigatórios.");
      event.preventDefault();
      return false;
    }

    // Check Email
    if (y === "" || !emailRegex.test(y)) {
      alert("Por favor, insira um endereço de e-mail válido.");
      event.preventDefault();
      return false;
    }

    // Check Telefone se fornecido
    if (a !== "" && !phoneRegex.test(a)) {
      alert("Por favor, insira um telefone válido no formato (xx) xxxx-xxxx.");
      event.preventDefault();
      return false;
    } else if (a !== "" && b === "null") {
      alert("Por favor, selecione o ddd também");
      event.preventDefault();
      return false;
    }

    // Se todas as validações estiverem corretas
    return true;
  });
});

  // <--- Função de Exibição Dinâmica de Conteúdo ---> //

  function dynamicDisplayDiv(divId) {
    // Esconde todas as divs
    document.getElementById('even').classList.add('hidden');
    document.getElementById('odd').classList.add('hidden');

    // Insere o conteúdo carregado na div correspondente
    var divToShow = document.getElementById(divId);
    divToShow.classList.remove('hidden');
  }

  function autoPhone(event) {
    const input = event.target;
    const valor = input.value;
    const tecla = event.key;
    const cursorPos = input.selectionStart;

    // Não processar se a tecla pressionada for Backspace, Delete ou Tab
    if (['Backspace', 'Delete', 'Tab'].includes(tecla)) return;

    // Remove caracteres não numéricos
    const apenasNumeros = valor.replace(/\D/g, '');

    // Adiciona a formatação
    const phoneFormat = apenasNumeros
      .replace(/(\d{2})(\d)/, '($1) $2')
      .replace(/(\d{5})(\d)/, '$1-$2');

    // Atualiza o valor do input
    input.value = phoneFormat;

    // Define a posição do cursor para acompanhar os replaces
    const position = cursorPos + (phoneFormat.length - valor.length);
    input.setSelectionRange(position, position);
  }


  // Função para criar as redes sociais no index

  function createMedias(social_medias) {
    const mediaContainer = document.getElementById('social-medias');
    mediaContainer.innerHTML = '';

    social_medias.forEach((media) => {
      // Verifica se o link da mídia não está vazio
      if (media.link !== "") {
        const a = document.createElement('a');
        a.href = media.link;
        a.id = `media-${media.id}`;
        a.target = '_blank';  // Abre o link em uma nova aba
        a.rel = 'noopener noreferrer';  // Melhora a segurança

        const img = document.createElement('img');
        img.src = media.image;
        img.alt = media.name;

        a.appendChild(img);
        mediaContainer.appendChild(a);
      }
    });
  }
