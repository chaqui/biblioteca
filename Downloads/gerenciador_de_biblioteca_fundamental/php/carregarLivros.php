<?php
/**
 * Retorna os livros para listagem
 * User: Dejean
 * Date: 08/02/2017
 * Time: 18:06
 */
define('SHORTINIT', false);
require_once("../../../../wp-load.php");
date_default_timezone_set('America/Sao_Paulo');
global $wpdb;
$acao = $_POST["Acao"];
if($acao == "trazerLivros"){
    $limit = $_POST["Limit"];
    $offset = $_POST["Offset"];
    $pesquisar = $_POST["Pesquisar"];
    if((empty($pesquisar)) || (is_null($pesquisar)) || ($pesquisar == "")){
        $livros = $wpdb->get_results("SELECT `ID`, `post_title` FROM `".$wpdb->prefix."posts` WHERE `post_type`='livro' AND `post_status`='publish' LIMIT $offset,$limit");
    }else{
        $livros = $wpdb->get_results("SELECT `ID`, `post_title` FROM `".$wpdb->prefix."posts` WHERE `post_type`='livro' AND `post_status`='publish' AND `post_title` LIKE '%".$pesquisar."%' LIMIT $offset,$limit");
    }
    $html = "";
    foreach($livros as $livro){
        $autor_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `post_id` = ".$livro->ID." AND `meta_key`='autor_livro'");
        $ano_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `post_id` = ".$livro->ID." AND `meta_key`='ano_publicacao_livro'");
        //TODO: Mostrar se ta reservado ou nao
        //Para isso, pegar o numero de livros cadastrados e o COUNT() dos reservados. Se o COUNT for igual ao n de cadastros, ta tudo reservado.
        $qtd_cadastrada = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='quantidade_livros' AND `post_id`=".$livro->ID."");
        $qtd_reservada = $wpdb->get_var("SELECT COUNT(id) as total FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `livro_id`=".$livro->ID." AND `status`!=2");
        if($qtd_reservada >= $qtd_cadastrada){
            $estoque = false;
        }else{
            $estoque = true; //Ainda tem no estoque
        }

        //se for menore, ainda tem algum pra reservar.
        $ano_livro = ($ano_livro != "") ? ", ".$ano_livro : "";
        $autor_livro = ($autor_livro != "") ? $autor_livro : "";
        $imagem_src = wp_get_attachment_image_src( get_post_thumbnail_id($livro->ID),  'medium' );
        $imagem_src = ($imagem_src != false) ? $imagem_src : array(plugin_dir_url(__FILE__)."../img/sem_imagem.jpg");
        $html .= '<div class="col-xs-12 col-md-4 livroItem" id="livro-'.$livro->ID.'">';
        $html .= '<div class="imagemLivroCapa"><img src="'.$imagem_src[0].'" style="width: 100%"/></div>';
        $html .= '<div class="detalhesLivro"><p class="tituloLivro">'.$livro->post_title.'</p><p class="autorLivro">'.$autor_livro.$ano_livro.'</p>';
        $html .= '<button type="button" style="border-radius: 4px;" class="btn'.(($estoque == false) ? "jaReservado btn-default btn-block" : "btnReservarLivro btn-success btn-block").'" '.(($estoque != false) ? 'onclick="reservarLivro('.$livro->ID.', \'reservaImediata\')"' : 'onclick="reservarLivro('.$livro->ID.', \'agendarReserva\')"').'>'.(($estoque == false) ? 'Reservado' : "Reservar").'</button>';
        $html .= '<button type="button" class="btn btn-block btn-info" onclick="visualizarDetalhesLivro('.$livro->ID.')">Ver Detalhes</button>';
        $html .= '</div>';
        $html .= '</div>';
    }
    echo $html;
}else if ($acao == "trazerDetalhes"){
    $livro_id = $_POST["Livro"];
    $titulo_livro = $wpdb->get_var("SELECT `post_title` FROM `".$wpdb->prefix."posts` WHERE `ID`=".$livro_id."");
    $numero_chamada = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='numero_chamada' AND `post_id`=".$livro_id."");
    $subtitulo = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='subtitulo' AND `post_id`=".$livro_id."");
    $titulo_original = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='titulo_original' AND `post_id`=".$livro_id."");
    $autor_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='autor_livro' AND `post_id`=".$livro_id."");
    $ano_publicacao = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='ano_publicacao_livro' AND `post_id`=".$livro_id."");
    $edicao_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='edicao_livro' AND `post_id`=".$livro_id."");
    $volume_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='volume_livro' AND `post_id`=".$livro_id."");
    $editora_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='editora_livro' AND `post_id`=".$livro_id."");
    $numero_paginas = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='nuimero_paginas_livro' AND `post_id`=".$livro_id."");
    $idioma_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='idioma_livro' AND `post_id`=".$livro_id."");
    $sasunto_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='assunto_livro' AND `post_id`=".$livro_id."");
    $genero_livro = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='genero_livro' AND `post_id`=".$livro_id."");

    $retorno_html = "";
    $retorno_html .= '<p style="font-size: 16px;color:#5e5e5e;margin-top:5px;margin-bottom: 15px;font-weight: bold;"><strong>Titulo: </strong> <span>' .$titulo_livro.'</span></p>';
    if((!empty($titulo_original)) && ($titulo_original != "") && (!is_null($titulo_original))){
        $retorno_html .= '<p><strong>Titulo Original: </strong> <span>'.$titulo_original.'</span></p>';
    }
    if((!empty($subtitulo)) && ($subtitulo != "") && (!is_null($subtitulo))) {
        $retorno_html .= '<p><strong>Subtítulo: </strong> <span>' . $subtitulo . '</span></p>';
    }
    if((!empty($numero_chamada)) && ($numero_chamada != "") && (!is_null($numero_chamada))) {
        $retorno_html .= '<p><strong>ID: </strong> <span>' . $numero_chamada . '</span></p>';
    }
    if((!empty($autor_livro)) && ($autor_livro != "") && (!is_null($autor_livro))) {
        $retorno_html .= '<p><strong>Autor: </strong> <span>' . $autor_livro . '</span></p>';
    }
    if((!empty($ano_publicacao)) && ($ano_publicacao != "") && (!is_null($ano_publicacao))) {
        $retorno_html .= '<p><strong>Ano de Publicação: </strong> <span>' . $ano_publicacao . '</span></p>';
    }
    if((!empty($edicao_livro)) && ($edicao_livro != "") && (!is_null($edicao_livro))) {
        $retorno_html .= '<p><strong>Edição do Livro: </strong> <span>' . $edicao_livro . '</span></p>';
    }
    if((!empty($volume_livro)) && ($volume_livro != "") && (!is_null($volume_livro))) {
        $retorno_html .= '<p><strong>Volume do Livro: </strong> <span>' . $volume_livro . '</span></p>';
    }
    if((!empty($editora_livro)) && ($editora_livro != "") && (!is_null($editora_livro))) {
        $retorno_html .= '<p><strong>Editora do Livro: </strong> <span>' . $editora_livro . '</span></p>';
    }
    if((!empty($numero_paginas)) && ($numero_paginas != "") && (!is_null($numero_paginas))) {
        $retorno_html .= '<p><strong>Número de Páginas: </strong> <span>' . $numero_paginas . '</span></p>';
    }
    if((!empty($idioma_livro)) && ($idioma_livro != "") && (!is_null($idioma_livro))) {
        $retorno_html .= '<p><strong>Idioma do Livro: </strong> <span>' . $idioma_livro . '</span></p>';
    }
    if((!empty($sasunto_livro)) && ($sasunto_livro != "") && (!is_null($sasunto_livro))) {
        $retorno_html .= '<p><strong>Assunto do Livro: </strong> <span>' . $sasunto_livro . '</span></p>';
    }
    if((!empty($genero_livro)) && ($genero_livro != "") && (!is_null($genero_livro))) {
        $retorno_html .= '<p><strong>Gênero do Livro: </strong> <span>'.$genero_livro.'</span></p>';
    }

    echo $retorno_html;
}else if ($acao == "confirmarReservaLivro"){
    //Confirma uma reserva para esse usuario...
    $livro_id = $_POST["Livro"];
    $nome_completo = $_POST["nome_completo"];
    $cpf = $_POST["cpf"];
    $endereco = $_POST["endereco"];
    $telefone = $_POST["telefone"];
    $email = $_POST["email"];
    $reservaImediata = $_POST['reservaImediata'];
    $data_hoje = date("Y-m-d H:i:s", time()); //guarda a data de hoje
    if($reservaImediata == "nao"){
        //nao ha mais unidades disponiveis para reservar hoje, entao será necessario agendar uma reserva
        $data_entrega = date("Y-m-d H:i:s", strtotime($wpdb->get_var("SELECT `config_value` FROM `" . $wpdb->prefix . "fundamental_livros_config` WHERE `config_name`='tempo_posse'") . " days")); //data de hoje + o prazo de retirada configurado, nao foi preciso programar o claculo de data da retirada quando há mais de um agendamento de reserva cadastrado, ao inves disto usamos a data do pedido de reserva e listamos por ordem de horario do primeiro ate o ultimo na tabela da area admin.
        $wpdb->query("INSERT INTO `" . $wpdb->prefix . "fundamental_livros_reservados` (`livro_id`, `quantidade_reservada`, `nome_reservista`, `endereco_reservista`, `telefone_reservista`, `email_reservista`, `cpf_Reservista`, `cidade_reservista`, `uf_reservista`, `data_de_reserva`, `status`, `data_de_entrega`, `reserva_imediata`) VALUES($livro_id,1,'$nome_completo', '$endereco','$telefone','$email', '$cpf','','','$data_hoje',0,'$data_entrega','nao')");
        //TODO: Notificar por email, que um novo livro foi reservado.
    }else {
        $data_entrega = date("Y-m-d H:i:s", strtotime($wpdb->get_var("SELECT `config_value` FROM `" . $wpdb->prefix . "fundamental_livros_config` WHERE `config_name`='tempo_posse'") . " days")); //data de hoje + o prazo de retirada configurado
        $wpdb->query("INSERT INTO `" . $wpdb->prefix . "fundamental_livros_reservados` (`livro_id`, `quantidade_reservada`, `nome_reservista`, `endereco_reservista`, `telefone_reservista`, `email_reservista`, `cpf_Reservista`, `cidade_reservista`, `uf_reservista`, `data_de_reserva`, `status`, `data_de_entrega`) VALUES($livro_id,1,'$nome_completo', '$endereco','$telefone','$email', '$cpf','','','$data_hoje',0,'$data_entrega')");
        //TODO: Notificar por email, que um novo livro foi reservado.
    }
    echo "OK";
}

function calcularAtraso($data_entrega){
    global $wpdb;
    //Se a data atual for superior a data de entrega, entao ta atrasado
    //$hoje = new DateTime(strtotime(time()));
    //$data_entrega = new DateTime($data_entrega);
    $hoje = strtotime(date("Y-m-d"));
    $data_entrega = strtotime(date("Y-m-d", strtotime($data_entrega)));
    if($hoje <= $data_entrega){
        return "<span style='color:#3e8a42;font-weight: bold'>Não há multa</span>";
    }else{
        //Calcula quanto de multa aplicar
        //1- quanto de multa se aplica por dia?
        $multa_diaria = $wpdb->get_var("SELECT `config_value` FROM `".$wpdb->prefix."fundamental_livros_config` WHERE `config_name`='multa_diaria_por_atraso'");
        //2- Quantos dias está atrasado?
        $dias_atrasados = ceil(abs($hoje - $data_entrega) / 86400);
        $multa_diaria = str_replace(",",".",$multa_diaria);
        //3- multiplica esse qnt pelos dias atrasados
        $multa_a_aplicar = number_format($multa_diaria * $dias_atrasados, 2);
        $ret = '<p style="color: #FF5722;font-weight: bold;margin-bottom: 0;">Dias atrasados: <span>'.$dias_atrasados.'</span></p>';
        $ret .= '<p style="color: #F44336;font-weight: bold;">Multa: <span style="font-size: 17px;">R$ '.str_replace(".",",",$multa_a_aplicar).'</span></p>';
        return $ret;
    }


    //descobre qts dias de atraso tem, e calcula a multa
}
if ($acao == "confirmarEntregLivro"){
    $reserva_id = $_POST["reserva"];
    $dados_reserva = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `id` = ".$reserva_id."");
    $dados_reserva = $dados_reserva[0];
    $html = '<div class="container-fluid">';
        $html .= '<div class="row">';
            $html .= '<div class="col-xs-12">';
            $html .= '<p><strong>Nome: </strong> '.$dados_reserva->nome_reservista.'</p>';
            $html .= '<p><strong>Endereço: </strong> '.$dados_reserva->endereco_reservista.'</p>';
            $html .= '<p><strong>Telefone: </strong> '.$dados_reserva->telefone_reservista.'</p>';
            $html .= '<p><strong>E-mail: </strong> '.$dados_reserva->email_reservista.'</p>';
            $html .= '<p><strong>CPF: </strong> '.$dados_reserva->cpf_Reservista.'</p>';
            //Foi renovado?
            $renovado_ja = $wpdb->get_var("SELECT `renovado` FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `id`=".$reserva_id."");
        if($renovado_ja > 0){
            //ja foi renovado, mostrea mensagem
            $html .= '<p style="font-size: 13px;font-weight: bold;color:#d10000">Houve ' .$renovado_ja.' solicitação(ões) de estensão de prazo.</p>';
        }
            $html .= '<p><strong>Data da reserva: </strong> '.date("d/m/Y H:i:s", strtotime($dados_reserva->data_de_reserva)).'</p>';
            $html .= '<p><strong>Data prevista de entrega: </strong> '.date("d/m/Y", strtotime($dados_reserva->data_de_entrega)).'</p>';
            $html .= '<p class="atrasoContainerInfo"><strong>Atraso: </strong> '.calcularAtraso($dados_reserva->data_de_entrega).'</p>';
            $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row">';
            $html .= '<div class="col-xs-12" id="containerBotaoConfirmarRecebimento">';
                $html .= '<button class="btn btn-block btn-success" title="Essa ação irá confirmar que o livro foi devolvido." onclick="darBaixa('.$reserva_id.', this)">Confirmar Recebimento</button>';
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</div>';
    echo $html;
}

if($acao == "liberarLivro"){
    $reserva_id = $_POST["reserva"];
    //Deleta da tabela de reserva
    $wpdb->query("DELETE FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `id`=".$reserva_id."");
    echo "OK";
}

if($acao == "entregarLivro"){
    // Confirma que essa retirada foi confirmada
    $status = 1;
    //2- atualiza a data de retirada para a atual data de confirmacao
    $data_hoje = date("Y-m-d H:i:s", time()); //guarda a data de hoje
    //3- atualiza a data de entrega, para dias a partir da data de confirmacao
    $data_entrega = date("Y-m-d H:i:s",strtotime($wpdb->get_var("SELECT `config_value` FROM `".$wpdb->prefix."fundamental_livros_config` WHERE `config_name`='tempo_posse'")." days")); //data de hoje + o prazo de retirada configurado
    //4 - id da reserva
    $reserva_id = $_POST["reserva"];

    //4 - se o cliente eestava aguardando estoque do livro para poder retira-lo
    if($_POST['reserva_imediata'] == "nao"){
        //entao atualiza o status de "Aguardando disponibilidade" para "Aguardando confirmação"
        $wpdb->query("UPDATE `".$wpdb->prefix."fundamental_livros_reservados` SET `reserva_imediata`='sim', `data_de_reserva`='".$data_hoje."', `data_de_entrega`='".$data_entrega."' WHERE `id`='".$reserva_id."'");
        echo "OK";
    }else{
        //senao é uma confirmaçao de retirada normal, quando o livro tem estoque
        //5- guarda os dados atualizados no banco
        $wpdb->query("UPDATE `".$wpdb->prefix."fundamental_livros_reservados` SET `status`=".$status.", `data_de_reserva`='".$data_hoje."', `data_de_entrega`='".$data_entrega."' WHERE `id`='".$reserva_id."'");
        echo "OK";
    }

}

if($acao == "salvarConfiguracoesPlugin"){
    $dias_que_pode_ficar = $_POST["Dias"];
    $multa = $_POST["Multa"];
    $wpdb->query("UPDATE `".$wpdb->prefix."fundamental_livros_config` SET `config_value`='".$multa."' WHERE `config_name`='multa_diaria_por_atraso'");
    $wpdb->query("UPDATE `".$wpdb->prefix."fundamental_livros_config` SET `config_value`='".$dias_que_pode_ficar."' WHERE `config_name`='tempo_posse'");
    echo "OK";
}

if($acao == "estenderPrazo"){
    $retirada_id = $_POST["retirada_id"];
    //Pega a data de entrega do livro
    $data_entrega = $wpdb->get_var("SELECT `data_de_entrega` FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `id`=".$retirada_id."");
    //Acrescenta mais dias a ela e guarda
    $data_entrega =strtotime("+ ".$wpdb->get_var("SELECT `config_value` FROM `".$wpdb->prefix."fundamental_livros_config` WHERE `config_name`='tempo_posse'")." days", strtotime($data_entrega)); //data de hoje + o prazo de retirada configurado
    $wpdb->query("UPDATE `".$wpdb->prefix."fundamental_livros_reservados` SET `data_de_entrega`='".date("Y-m-d H:i:s", $data_entrega)."' WHERE `id`=".$retirada_id."");
    //Incrementa o campo "renovado"
    $renovado_vzs = $wpdb->get_var("SELECT `renovado` FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `id`=".$retirada_id."");
    $wpdb->query("UPDATE `".$wpdb->prefix."fundamental_livros_reservados` SET `renovado` = ".($renovado_vzs+1)." WHERE `id`=".$retirada_id."");
    echo "OK";
}

if($acao == "excluirEntrada"){
    $entrada = $_POST["Entrada"];
    $wpdb->query("DELETE FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `id`=".$entrada."");
    echo "OK";
}
