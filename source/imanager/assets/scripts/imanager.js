//
//  Variaveis globais de ajuda
//
const BASE_URL = $('#implugin-base-path').val() + 'imanager/';
const ERROR_MESSAGE = '<p style="margin: 15px;">Há um problema com sua conexão com a internet, tente novamente mais tarde.</p>';
const ERROR_ALERT = '<div class="container"><div class="alert alert-danger" role="alert"><p>Há um problema com sua conexão com a internet, tente novamente mais tarde.</p></div></div>';
const MODAL_NAME = '#imanager-modal';

//
// Carrega uma lista de imagem durante o 
// o carregamento inicial da página
//
$(document).ready(function (ev) {
    loadContent('.image-panel', 'load-image-list');
});

//
// Evento de click no documento
//
// Responsável por remover a classe de seleção no item de imagem atual
// Caso o click seja em um item de .image ou .image-info-footer encerra
// a execução antes de remover a classe .selected.
//
$(document).click(function (ev) {
    var self = $(ev.target);
    if (self.closest('.thumbnail').length > 0 || self.closest('.image-info-footer').length > 0) {
        return true;
    }
    $('.image').removeClass('selected');
});

//
// Evento de entrada do mouse em um .image
//
// Responsável por gerenciar o evento de entrada do mouse em um .image
// caso nenhuma .image esteja selecioanda.
//
$('body').on('mouseenter', '.image', function (ev) {
    // Mostra o foooter
    $('.image-info-footer').fadeIn(500);
    if (!$('.selected').length) {
        selectDisplay(this);
        ev.preventDefault();
    }
});

//
// Evento de saida do mouse em um .image
//
$('body').on('mouseleave', '.image', function (ev) {
    ev.preventDefault();
});

//
// Evento de click em um .image
// Responsável por fazer a seleção de determinado item,
// ou se for o caso desmarcar o item selecionado.
//
// Ao retirar a tag .selected o painel de visualização inferior fica liberado 
// para mostrar as informações do item que o mouse está em cima.
//
$('body').on('click', '.image', function (ev) {
    var self = $(ev.target);
    var selected = (self.closest('.selected').length > 0);
    var is_item = (self.closest('.image').length > 0);
    // Retira todos os .selected
    $('.image').removeClass('selected');
    // Marca ou desmarca determinado item
    if (!selected) {
        $(this).addClass('selected');
    }
    // Seleciona valor atual
    if (is_item) {
        selectDisplay(this);
        ev.preventDefault();
    }
});

//
// Evento de click em um .overlay
// Responsável por iniciar um modal com a imagem em tamanho grande.
//
$('body').on('click', '.overlay', function (ev) {
    showMessage('<img src="' + $('.image-info-display #path').val() + '" style="max-width: 100%;" />');
});

//
// Evento de click na opção de seleção
// Retorna para o FKEditor a URL da imagem selecionada 
// 
$('body').on('click', '#btn-img-select', function (ev) {
    returnFileUrl();
});

//
// Evento de click na opção de upload (Botão Adicionar)
//
// Inicia o plugin Bootstrap File Dialog (@link https://github.com/Saluev/bootstrap-file-dialog)
//
$('body').on('click', '#btn-img-upload', function (ev) {
    $.FileDialog({
        accept: ['image/png', 'image/jpeg', 'image/gif'],
        cancel_button: "Fechar",
        drag_message: 'Arraste e solte seus arquivos aqui.',
        error_message: 'Ocorreu um problema ao carregar a imagem',
        remove_message: 'Remover arquivo',
        title: 'Enviar arquivos'
    });
});

//
// Evento de click em botão de carregamento de mais imagens
//
// Faz requisição de carregamento de mais itens 
//
$('body').on('click', '.btn-img-more', function (ev) {
    var data = requestForm('load-image-list');
    data['implugin-search'] = $('.current-search').val();
    data['implugin-page'] = $('.btn-img-more').attr('id');
    $('.more').remove();
    appendContent('.image-panel', '', data);
});

// Evento de click na opção de pesquisa
//
// Gera um objeto de formulário com as informações necessárias para a requisição
// de listagem de imagens com as palavras digitadas e envia a requisição
// utilizando o método de carregamento de conteúdo (loadContent).
//
// @todo pesquisar com ENTER
//
$('body').on('click', '#btn-img-search', function (ev) {
    var data = requestForm('load-image-list');
    data['implugin-search'] = $('#implugin-search').val();
    loadContent('.image-panel', '', data);
});

//
// Evento de click na opção de edição 
//
// Gera um objeto de formulário com as informações necessárias
// para o carregamento do menu de edição de imagem, enviando essa requisição
// utilizando o método de carregamento de conteúdo (loadContent).
//
$('body').on('click', '#btn-img-edit', function (ev) {
    var data = requestForm('edit-image');
    data['implugin-id'] = $('#implugin-selected').val();
    loadModal(data, true);
});

//
// Evento de click na opção de confirmação de edição
// 
// Gera um objeto de formulário com as informações necessárias
// para renomear a imagem.
// Mostra o feedback da edição.
//
$('body').on('click', '#btn-img-edit-confirm', function (ev) {
    var data = requestForm('edit-image');
    data['implugin-id'] = $('#implugin-selected').val();
    data['implugin-name'] = $('#edit-image-name').val();
    data['implugin-confirm-action'] = true;
    sendForm(data, '.image-panel', 'load-image-list', ev);
    $('.image-info-footer').fadeOut(500);
});

//
// Evento de digitação no formulário de edição
// 
// Caso digite ENTER, envia o formulário
//
$('body').on('keypress', '#edit-image-name', function (ev) {
    if (ev.which == 13) {
        ev.preventDefault();
        $('#btn-img-edit-confirm').click();
        return;
    }
});

//
// Evento que mostra modal
//
// Da foco para input que possua a tag autofocus
//
$('#feedback-modal').on('shown.bs.modal', function () {
    $(this).find('[autofocus]').focus();
});
//
// Evento de click na opção de exclusão 
//
// Gera um objeto de formulário com as informações necessárias
// para o carregamento do menu de exclusão de imagem, enviando essa requisição
// utilizando o método de carregamento de conteúdo (loadContent). 
//
$('body').on('click', '#btn-img-delete', function (ev) {
    var data = requestForm('delete-image');
    data['implugin-id'] = $('#implugin-selected').val();
    loadModal(data, true);
});

//
// Evento de click na opção de confirmação de exclusão
// 
// Gera um objeto de formulário com as informações necessárias
// para excluir a imagem.
// Mostra o feedback da exclusão.
//
$('body').on('click', '#btn-img-delete-confirm', function (ev) {
    var data = requestForm('delete-image');
    data['implugin-id'] = $('#implugin-selected').val();
    data['implugin-confirm-action'] = true;
    sendForm(data, '.image-panel', 'load-image-list', ev);
    $('.image-info-footer').fadeOut(500);
});

//
// Evento de click na confirmação do modal de upload 
//
// Gera um objeto de formulário com as informações necessárias
// para o upload de um arquivo, e envia utilizando método de envio de formulários (sendForm).
//
$('body').on('files.bs.filedialog', function (ev) {
    var data = new FormData();
    data.append('implugin-secret-key', $('#implugin-secret-key').val());
    data.append('implugin-selected-action', 'upload-image');
    $.each(ev.files, function (i, file) {
        data.append('implugin-file-' + i, file);
    });
    sendFormMultipart(data, '.image-panel', 'load-image-list', ev);
});

//
// Envia as informações do .image 
// para o painel de inferior de informações
//
// @param self .image alvo
//
function selectDisplay(self)
{
    var id = $(self).attr('id');
    var from = '.image-info-' + id;
    var path = $(from).find('#path').val();
    var thumb = $(from).find('#thumb').val();
    var dimensions = $(from).find('#width').val() + 'x' + $(from).find('#height').val();
    $('#implugin-selected').val(id);
    $('.image-info-display #name').html($(from).find('#name').val());
    $('.image-info-display #type').html($(from).find('#type').val());
    $('.image-info-display #size').html($(from).find('#size').val());
    $('.image-info-display #dimensions').html(dimensions);
    $('.image-info-display #thumb').val(thumb);
    $('.image-info-display #path').val(path);
    $('#btn-img-download').attr('href', path);
    $('.image-info-gallery').attr('src', thumb);
    $('.image-info-display #created').html($(from).find('#created').val());
}

//
// Gera um objeto de formulário básico $_POST (não multipart/form-data)
// 
// O formulário já possui a secret-key e a ação que será executada no back-end.
//
// @param method método do back-end que será executado
// @return formulário não multipart
// 
function requestForm(method)
{
    var data = {};
    data['implugin-secret-key'] = $('#implugin-secret-key').val();
    data['implugin-selected-action'] = method;
    return data;
}

//
// Envia formulário multipart/form-data e carrega resposta
//
// A função permite o envio de um formulário e o carregamento de um determinado
// conteudo dentro de um DOM.
// Assim é possível fazer determinada requisição e em seguida recarregar 
// a lista de imagem.
// Também dispara um modal com a resposa da requisição.
//
// @param data objeto do formulário
// @param contentTarget alvo do recarregamento de informações da lista
// @param response_method método do recarregamento de informações da lista
// @param event evento gerador
// 
function sendFormMultipart(data, contentTarget, response_method, event) {
    // Imagem de carregamento
    loading();
    event.preventDefault();
    $(this).unbind();
    // Envia formulário
    $.ajax({
        type: 'POST',
        url: BASE_URL + 'api/index.php',
        cache: false,
        contentType: false,
        processData: false,
        data: data
    }).done(function (response, code) {
        if (code == 'success') {
            // Carrega conteúdo
            loadContent(contentTarget, response_method);
        } else {
            response = ERROR_MESSAGE;
        }
        showMessage(response);
    }).fail(function (response, code) {
        showMessage(ERROR_MESSAGE);
        loadingComplete();
    })
}

//
// Envia formulário e carrega resposta
//
// A função permite o envio de um formulário e o carregamento de um determinado
// conteudo dentro de um DOM.
// Também dispara um modal com a resposa da requisição.
//
// @param data objeto do formulário
// @param contentTarget alvo do recarregamento de informações da lista
// @param response_method método do recarregamento de informações da lista
// @param event evento gerador
// 
function sendForm(data, contentTarget, response_method, event) {
    // Imagem de carregamento
    loading();
    event.preventDefault();
    $(this).unbind();
    // Envia formulário
    $.ajax({
        type: 'POST',
        url: BASE_URL + 'api/index.php',
        data: data
    }).done(function (response, code) {
        if (code == 'success') {
            // Carrega conteúdo
            loadContent(contentTarget, response_method);
        } else {
            response = ERROR_MESSAGE;
        }
        showMessage(response);
    }).fail(function (response, code) {
        showMessage(ERROR_MESSAGE);
        loadingComplete();
    })
}

//
// Função de carregamento de conteúdo
//
// Carrega o conteúdo de uma requisição em DOM alvo, utilizando um método
// que será interpretado pela back-end (method) ou um formulário pronto (data)
// Também possibilita a escolha de utilizar a barra de carregamento ou não.
// 
// @param target DOM alvo
// @param method método para back-end (se data for enviado, não será usado)
// @param data objeto do formulário (OPTATIVO)
// @param loading_bar utilizará barra de carregamento? (OPTATIVO)
//
function loadContent(target, method, data, loading_bar) {
    $(target).html('');
    // Valores padrões
    if (loading_bar == undefined) { var loading_bar = true; }
    if (data == undefined) { var data = requestForm(method); }
    // Carregamento de barra?
    if (loading_bar == true) { loading(); }
    $.ajax({
        type: 'POST',
        url: BASE_URL + 'api/index.php',
        data: data
    }).done(function (result, code){
        if (code == 'success') {
            $(target).html(result);
        } else {
            $(target).html(ERROR_ALERT);
        }
        loadingComplete();
    }).fail(function (result) {
        $(target).html(ERROR_ALERT);
        loadingComplete();
    });
}

//
// Função de carregamento de conteúdo sem exclusão do conteúdo atual
//
// Carrega o conteúdo de uma requisição em DOM alvo sem excluir os DOMs que já existem no objeto,
// utilizando um método que será interpretado pela back-end (method) ou um formulário pronto (data).
// Também possibilita a escolha de utilizar a barra de carregamento ou não.
// 
// @param target DOM alvo
// @param method método para back-end (se data for enviado, não será usado)
// @param data objeto do formulário (OPTATIVO)
// @param loading_bar utilizará barra de carregamento? (OPTATIVO)
//
function appendContent(target, method, data, loading_bar) {
    $(target).append('');
    // Valores padrões
    if (loading_bar == undefined) { var loading_bar = true; }
    if (data == undefined) { var data = requestForm(method); }
    // Carregamento de barra?
    if (loading_bar == true) { loading(); }
    $.ajax({
        type: 'POST',
        url: BASE_URL + 'api/index.php',
        data: data
    }).done(function (result, code) {
        if (code == 'success') {
            $(target).append(result);
        } else {
            $(target).append(ERROR_ALERT);
        }
       loadingComplete();
    }).fail(function (result) {
        $(target).append(ERROR_ALERT);
        loadingComplete();
    });
}

//
// Carrega conteúdo dentro do modal
// 
// Faz requisição $.ajax e carrega dentro do modal de feedback.
// Também possibilita a escolha de utilizar a barra de carregamento ou não.
// 
// @param data objeto do formulário
// @param loading_bar utilizará barra de carregamento?
//
function loadModal(data, loading_bar) {
    $('#feedback-modal-content').html('');
    if (loading_bar == undefined) { var loading_bar = true; }
    if (loading_bar == true) { loading(); }
    $.ajax({
        type: 'POST',
        url: BASE_URL + 'api/index.php',
        data: data
    }).done(function (result, code) {
        if (code == 'success') {
            showMessage(result);
        } else {
            showMessage(ERROR_MESSAGE);
        }
        loadingComplete();
    }).fail(function (result) {
        showMessage(ERROR_MESSAGE);
        loadingComplete();
    });
}

//
// Mostra o modal de Feedback
//
function showMessage(message) {
    $('#feedback-modal-content').html(message);
    $('#feedback-modal').modal('show');
}
//
// Mostra barra de carregamento 
//
function loading() {
    $('.loading').show(500);
}

//
// Esconde barra de carregamento 
//
function loadingComplete() {
    $('.loading').hide(500);
}

//
// Inicio de FKEditor 
//

function getUrlParam(paramName) {
    var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
    var match = window.location.search.match(reParam);

    return (match && match.length > 1) ? match[1] : null;
}

function returnFileUrl() {

    var funcNum = getUrlParam('CKEditorFuncNum');
    var fileUrl = $('.image-info-display #path').val();
    window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
    window.close();
}

//
// Final de FKEditor
//