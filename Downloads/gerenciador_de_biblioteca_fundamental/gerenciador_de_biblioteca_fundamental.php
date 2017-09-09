<?php
/**
* Plugin Name: Gerenciador de biblioteca
* Plugin URI: https://www.fundamental.digital/
* Description: Com esse plugin, é possível gerenciar o cadastro de livros, acompanhar as retiradas e entradas, além de buscar e consultar livros.
* Version: 1.0
* Author: Fundamental
* Author URI: https://www.fundamental.digital/
* License: GPL12
*/


register_activation_hook( __FILE__, 'gerenciador_de_biblioteca_fundamental_activation' );
function gerenciador_de_biblioteca_fundamental_activation(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'fundamental_livros_reservados';
    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		livro_id mediumint(9) NOT NULL,
		quantidade_reservada mediumint(9) NOT NULL,
		nome_reservista varchar(300) NOT NULL,
		endereco_reservista varchar(500) NOT NULL,
		telefone_reservista varchar(30),
		email_reservista varchar(250),
		cpf_Reservista varchar(30),
		cidade_reservista varchar(200),
		uf_reservista varchar(2),
		reserva_imediata varchar(15) DEFAULT 'sim' NOT NULL,
		data_de_reserva datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		data_de_entrega datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		renovado mediumint(9) DEFAULT '0' NOT NULL,
		status int NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

$table_name2 = $wpdb->prefix . 'fundamental_livros_config';
    $sql2 = "CREATE TABLE $table_name2 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		config_name varchar(200) NOT NULL,
		config_value varchar(200) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    dbDelta( $sql2 );

	global $wpdb;
	$wpdb->insert(
		$table_name2,
		array(
			'config_name' => "tempo_posse",
			'config_value' => "7"
		)
	);
	$wpdb->insert(
		$table_name2,
		array(
			'config_name' => "multa_diaria_por_atraso",
			'config_value' => "0,00"
		)
	);
}

add_action( 'init', 'post_type_livro_init' );

/**
 * Add meta box
 *
 * @param post $post The post object
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
function livro_add_meta_boxes( $post ){
	add_meta_box("livros_meta_box", "Informações do livro", "func_livros_meta_box", "livro", "normal", "low" );
}
add_action("add_meta_boxes_livro", "livro_add_meta_boxes");
/**
 * Funcao que renderiza o conteudo do meta box
 *
 * @param post $post The post object
 */
function func_livros_meta_box( $post ){
	wp_nonce_field( basename( __FILE__ ), "livros_meta_box_nonce");
	$numero_chamada = get_post_meta( $post->ID,"numero_chamada", true );
	$subtitulo = get_post_meta( $post->ID,"subtitulo", true );
	$titulo_original = get_post_meta( $post->ID,"titulo_original", true );
	$autor_livro = get_post_meta( $post->ID,"autor_livro", true );
	$ano_publicacao_livro = get_post_meta( $post->ID,"ano_publicacao_livro", true );
	$edicao_livro = get_post_meta( $post->ID,"edicao_livro", true );
	$volume_livro = get_post_meta( $post->ID,"volume_livro", true );
	$editora_livro = get_post_meta( $post->ID,"editora_livro", true );
	$nuimero_paginas_livro = get_post_meta( $post->ID,"nuimero_paginas_livro", true );
	$idioma_livro = get_post_meta( $post->ID,"idioma_livro", true );
	$assunto_livro = get_post_meta( $post->ID,"assunto_livro", true );
	$genero_livro = get_post_meta( $post->ID,"genero_livro", true );
	$quantidade_livros = get_post_meta( $post->ID,"quantidade_livros", true );?><div class="inside"><p>
		<label>Número Chamada</label><br/>
		<input type="text" name="numero_chamada" id="numero_chamada" placeholder="Digite o número da chamada" value="<?php echo $numero_chamada; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Subtítulo</label><br/>
		<input type="text" name="subtitulo" id="subtitulo" placeholder="Digite o subtítulo (Se houver)" value="<?php echo $subtitulo; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Título Original</label><br/>
		<input type="text" name="titulo_original" id="titulo_original" placeholder="Digite o título original do livro (Se necessário)" value="<?php echo $titulo_original; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Autor</label><br/>
		<input type="text" name="autor_livro" id="autor_livro" placeholder="Digite o nome do autor" value="<?php echo $autor_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Ano de Publicação</label><br/>
		<input type="number" name="ano_publicacao_livro" id="ano_publicacao_livro" placeholder="" value="<?php echo $ano_publicacao_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Edição</label><br/>
		<input type="text" name="edicao_livro" id="edicao_livro" placeholder="Ano de edição do livro" value="<?php echo $edicao_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Volume</label><br/>
		<input type="text" name="volume_livro" id="volume_livro" placeholder="Volume do livro (Se houver)" value="<?php echo $volume_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Editora</label><br/>
		<input type="text" name="editora_livro" id="editora_livro" placeholder="Editora do livro" value="<?php echo $editora_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Número de páginas</label><br/>
		<input type="numbrer" name="nuimero_paginas_livro" id="nuimero_paginas_livro" placeholder="Número de páginas" value="<?php echo $nuimero_paginas_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Idioma</label><br/>
		<input type="text" name="idioma_livro" id="idioma_livro" placeholder="Idioma do livro" value="<?php echo $idioma_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Assunto</label><br/>
		<input type="text" name="assunto_livro" id="assunto_livro" placeholder="Assunto do livro" value="<?php echo $assunto_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Gênero</label><br/>
		<input type="text" name="genero_livro" id="genero_livro" placeholder="Gênero do livro" value="<?php echo $genero_livro; ?>" style="width: 100%;height: 32px;"/>
	</p>
	<p>
		<label>Quantidade</label><br/>
		<input type="number" name="quantidade_livros" id="quantidade_livros" placeholder="Quantidade de livros" value="<?php echo $quantidade_livros; ?>" style="width: 100%;height: 32px;"/>
	</p>
	</div>
	<?php
}
/**
 * Salva os dados dos meta fields
 *
 * @param int $post_id The post ID.
 */
function livro_save_meta_boxes_data( $post_id ){
	if ( !isset( $_POST["livros_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["livros_meta_box_nonce"], basename( __FILE__ ) ) ){
		return;
	}
	// return if autosave
	if ( defined("DOING_AUTOSAVE") && DOING_AUTOSAVE ){
		return;
	}
	// Check the users permissions.
	if (!current_user_can("edit_post", $post_id ) ){
		return;
	}

	if ( isset( $_REQUEST["numero_chamada"] ) ) {
		update_post_meta( $post_id, "numero_chamada", sanitize_text_field($_POST["numero_chamada"]) );
	}

	if ( isset( $_REQUEST["subtitulo"] ) ) {
		update_post_meta( $post_id, "subtitulo", sanitize_text_field($_POST["subtitulo"]) );
	}

	if ( isset( $_REQUEST["titulo_original"] ) ) {
		update_post_meta( $post_id, "titulo_original", sanitize_text_field($_POST["titulo_original"]) );
	}

	if ( isset( $_REQUEST["autor_livro"] ) ) {
		update_post_meta( $post_id, "autor_livro", sanitize_text_field($_POST["autor_livro"]) );
	}

	if ( isset( $_REQUEST["ano_publicacao_livro"] ) ) {
		update_post_meta( $post_id, "ano_publicacao_livro", sanitize_text_field($_POST["ano_publicacao_livro"]) );
	}

	if ( isset( $_REQUEST["edicao_livro"] ) ) {
		update_post_meta( $post_id, "edicao_livro", sanitize_text_field($_POST["edicao_livro"]) );
	}

	if ( isset( $_REQUEST["volume_livro"] ) ) {
		update_post_meta( $post_id, "volume_livro", sanitize_text_field($_POST["volume_livro"]) );
	}

	if ( isset( $_REQUEST["editora_livro"] ) ) {
		update_post_meta( $post_id, "editora_livro", sanitize_text_field($_POST["editora_livro"]) );
	}

	if ( isset( $_REQUEST["nuimero_paginas_livro"] ) ) {
		update_post_meta( $post_id, "nuimero_paginas_livro", sanitize_text_field($_POST["nuimero_paginas_livro"]) );
	}

	if ( isset( $_REQUEST["idioma_livro"] ) ) {
		update_post_meta( $post_id, "idioma_livro", sanitize_text_field($_POST["idioma_livro"]) );
	}

	if ( isset( $_REQUEST["assunto_livro"] ) ) {
		update_post_meta( $post_id, "assunto_livro", sanitize_text_field($_POST["assunto_livro"]) );
	}

	if ( isset( $_REQUEST["genero_livro"] ) ) {
		update_post_meta( $post_id, "genero_livro", sanitize_text_field($_POST["genero_livro"]) );
	}

	if ( isset( $_REQUEST["quantidade_livros"] ) ) {
		update_post_meta( $post_id, "quantidade_livros", sanitize_text_field($_POST["quantidade_livros"]) );
	}


}
add_action("save_post_livro", "livro_save_meta_boxes_data", 10, 2 );

/****
 * Registra o shortcode de listagem e busca de livro
 * --------------------------------------------------
 */
function fundamental_listar_pesquisar_livros($atts){
    //Pega todos os livros
	$html ='<!-- Modal -->
<div id="mainLightbox" class="modal fade" role="dialog">
  <div class="modal-dialog" style="padding-top: 5%">
    <div class="modal-content">
      <div class="modal-body">
        <div id="modalLoader" style="text-align: center; padding: 60px 0;"><img src="'.plugin_dir_url(__FILE__). '/img/ajax-loader.gif"/></div>
        <div id="modalConteudo" style="display:none;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<style>
div#modalConteudo p {
    margin-bottom: 2px;
    font-size: 12px;
    color: #979797;
}
button.jaReservado {
    background: #b3b3b3;
}

</style>
';
	//Mostra o HTML de listagem e pesquisa
    $html .= '<div id="mainContainerLivros">';
        /* Container de pesquisa */
        $html .= '<div id="mainContainerSearchLivros" class="row"><form method="post" action="">';
            $html .= '<h4 class="buscaraLivroTitulo" style="margin-top: 0 !important;">Buscar livro:</h4>';
            $html .= '<div class="col-xs-12 col-md-8">';
                $html .= '<input type="text" name="palavraChave" id="palavraChave" placeholder="Digite o nome do livro" value="'.((isset($_POST["palavraChave"])) ? $_POST["palavraChave"] : "").'"/>';
            $html .= '</div>';
            $html .= '<div class="col-xs-12 col-md-4">';
                $html .= '<button type="submit" name="enviarForm" class="btn btn-success" id="btnEnviarForm">Buscar</button>';
            $html .= '</div>';
        $html .= '</form></div>';
    $html .= '</div>';
    $html .= '<div class="fundamental_livros_resultados row">';
    $html .= '</div><br/>';
    $html .= '<div class="fundamental_livros_loadMore">';
        $html .= '<button class="btnLoadMore" onclick="trazerLivros(this)">Carregar Mais</button>';
    $html .= '</div>';
        $html .= '<script type="text/javascript">
        var paginaAtual = 20; //limite de itens por pagina
        var offset = 0;
        function trazerLivros(btnLoadMais){
            jQuery(btnLoadMais).html("Aguarde...").attr("disabled", true);
            jQuery.ajax({
                type: "POST",
                url: "'.plugin_dir_url(__FILE__)."php/carregarLivros.php".'",
                data:{
                    Acao: "trazerLivros",
                    Limit: paginaAtual,
                    Pesquisar: jQuery("#palavraChave").val(),
                    Offset: offset
                }
            }).done(function(retHtml){
                jQuery(".fundamental_livros_resultados").append(retHtml);
                jQuery(btnLoadMais).html("Carregar Mais").attr("disabled", false);
                offset += 20;
            });
        }
        function visualizarDetalhesLivro(livro_id){
			jQuery("#modalConteudo").hide();
          	jQuery("#modalLoader").show();
			jQuery("#modalConteudo").html("");
        	jQuery("#mainLightbox").modal({backdrop:"static", keyboard:false});
        	jQuery("#mainLightbox").modal("show");
        	jQuery.ajax({
                type: "POST",
                url: "'.plugin_dir_url(__FILE__)."php/carregarLivros.php".'",
                data:{
                    Acao: "trazerDetalhes",
                    Livro: livro_id
                }
            }).done(function(retornoDetalhes){
                jQuery("#modalLoader").hide();
                jQuery("#modalConteudo").html(retornoDetalhes);
                jQuery("#modalConteudo").show();
            });
        }
        function reservarLivro(livro_id, reserva){
        if(reserva == "agendarReserva"){
     		var formCadastro = "<div id=\'agendarReserva\' style=\' font-size: 13px; text-align: justify; margin-bottom: 10px;\'>O livro que você está tentanto reservar, não possue nenhum exemplar disponivel para retirada no momento. No entando, o administrador do site irá agendar sua retirada para quando houver um exemplar disponivel.</div>";
		}else{
     	//eh uma reserva normal
    		 var formCadastro="";
		}
        	formCadastro += \'<div class="form-group"><input type="text" name="nomeReservista" class="form-control" placeholder="Digite seu nome completo"/></div><div class="form-group"><input type="text" name="cpfReservista" class="form-control" placeholder="Digite seu CPF (Apenas números)"/></div><div class="form-group"><input type="text" name="enderecoReservista" class="form-control" placeholder="Digite seu Endereço"/></div><div class="form-group"><input type="text" name="telefoneReservista" class="form-control" placeholder="Digite seu telefone"/></div><div class="form-group"><input type="text" name="emailReservista" class="form-control" placeholder="Digite seu e-mail"/></div><div class="form-group"><button class="btnReservarLivro btn-block" onclick="confirmarReserva(\'+livro_id+\')">Reservar livro</button></div>\';
        	jQuery("#modalConteudo").html("");
        	jQuery("#modalLoader").hide();
			jQuery("#modalConteudo").append(formCadastro);
			jQuery("#modalConteudo").show();
			jQuery("#mainLightbox").modal({backdrop:"static", keyboard:false});
        	jQuery("#mainLightbox").modal("show");
        }
        //valida os campos
        function validarCampos(){
        	if((jQuery("input[name=\'nomeReservista\']").val() =="") || (jQuery("input[name=\'cpfReservista\']").val() =="") || (jQuery("input[name=\'enderecoReservista\']").val() =="") || (jQuery("input[name=\'emailReservista\']").val() =="")){
        		alert("Por favor, preencha corretamente os campos");
        		return false;
        	}else{
        		return true;
        	}
        }
        //Confirma a reserva do livro
        function confirmarReserva(livro_id){
				//valida os campos
			if(validarCampos()){
			jQuery("button.btnReservarLivro").html("Aguarde...").attr("disabled", true);
				jQuery.ajax({
					type: "POST",
					url: "'.plugin_dir_url(__FILE__)."php/carregarLivros.php".'",
					data:{
						Acao: "confirmarReservaLivro",
						reservaImediata: (jQuery("#agendarReserva").length) ? "nao" : "sim",
						Livro: livro_id,
						nome_completo: jQuery("input[name=\'nomeReservista\']").val(),
						cpf: jQuery("input[name=\'cpfReservista\']").val(),
						endereco: jQuery("input[name=\'enderecoReservista\']").val(),
						telefone: jQuery("input[name=\'telefoneReservista\']").val(),
						email: jQuery("input[name=\'emailReservista\']").val()

					}
				}).done(function(retornoConfirmacao){
					if(retornoConfirmacao == "OK"){
						jQuery("button.btnReservarLivro").html("Reservar livro").attr("disabled", false);
						//Reserva bem sucedida
						alert("Livro reservado com sucesso!");
						window.location.reload();
					}
				});
			}
        }
        jQuery(document).ready(function(){
            trazerLivros();
        });
        ';
    $html .= '</script>';
    return $html;
}
add_shortcode("fundamental_livros", "fundamental_listar_pesquisar_livros");

/**
 * Registra uma função para mostrar uma tela/painel para o usuario consultar serus livros
 * E poder expandir a data de entrega
 * @param $atts
 */
function func_fundamental_livros_perfil($atts){
	global $wpdb;
	$html = "";
	if((isset($_POST["cpfLivroUsuario"]))){
		if(!empty($_POST["cpfLivroUsuario"])){
			//1- consulta o CPF no banco
			$cpf_existe = $wpdb->get_var("SELECT COUNT(id) FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `cpf_Reservista`='".$_POST["cpfLivroUsuario"]."'");
			if($cpf_existe > 0){
				//Esse CPF existe, é um usuario com livros cadastrados.
				//pega os dados e os livros dele.
				$dados = $wpdb->get_results("SELECT l.*, p.post_title FROM `".$wpdb->prefix."fundamental_livros_reservados` as l JOIN `".$wpdb->prefix."posts` as p ON l.livro_id = p.ID WHERE l.cpf_Reservista ='".$_POST["cpfLivroUsuario"]."'");

				//pega a quantia de reservas feitas pra este livro do usuario atual
				for ($i = 0; $i < count(array($dados)); $i++) {
					$dds = $dados[$i];
				}

				$html .= '
			<div id="modalEstenderPrazo" class="modal fade" role="dialog">
			  <div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Estender o prazo</h4>
				  </div>
				  <div class="modal-body">
					<p>Essa ação irá solicitar que o prazo de entrega seja estendido por mais <strong>'.$wpdb->get_var("SELECT `config_value` FROM `".$wpdb->prefix."fundamental_livros_config` WHERE `config_name` = 'tempo_posse'").' dias</strong><br/>Deseja prosseguir?</p>
				  </div>
				  <div class="modal-footer">
				  <input type="hidden" name="idConfirmar" value="x" id="idConfirmar"/>
					<button type="button" class="btn btn-success" onclick="triggerConfirmacao(this)">Confirmar</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				  </div>
				</div>
			  </div>
			</div>';
				$html .= '<script type="text/javascript">
				function estenderRetirada(retirada_id){
					jQuery("#modalEstenderPrazo").modal({"backdrop": "static", "keyboaard": false});
					jQuery("#idConfirmar").val(retirada_id);
					jQuery("#modalEstenderPrazo").modal("show");
				}

				function triggerConfirmacao(btnConfirmar){
					jQuery(btnConfirmar).html("Aguarde...").attr("disabled", true);
					jQuery.ajax({
					    type: "POST",
					    url: "'.plugin_dir_url(__FILE__).'php/carregarLivros.php",
					    data:{
							Acao: "estenderPrazo",
							retirada_id: jQuery("#idConfirmar").val()
						}
					}).done(function(ret){
						if(ret="OK"){
							alert("Prazo estendido com sucesso!");
							window.location.reload();
						}
					});
				}
			</script>';
				$html .= '<div class="container-fluid" style="font-size: 13px;">';
				$html .= '<div class="row">';
				$html .= '<div class="col-xs-12">';
				$html .= '<p><strong>Nome: </strong> <span>'.$dados[0]->nome_reservista.'</span></p>';
				$html .= '<p><strong>E-mail: </strong> <span>'.$dados[0]->email_reservista.'</span></p>';
				$html .= '<p><strong>Telefone: </strong> <span>'.$dados[0]->telefone_reservista.'</span></p>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div class="row">';
				$html .= '<div class="col-xs-12">';
				$html .= '<table class="table table-striped">';
				$html .= '<thead>';
				$html .= '<tr>';
				$html .= '<td>Livro</td>';
				$html .= '<td>Qtd.</td>';
				$html .= '<td>Retirada</td>';
				$html .= '<td>Entrega</td>';
				$html .= '<td>Opções</td>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tbody>';
				foreach($dados as $livro){
				    //pega a quantia de livros cadatrados
				    $qtd_cadastrada = $wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."postmeta` WHERE `meta_key`='quantidade_livros' AND `post_id`=".$livro->livro_id."");
                    $qtd_reservada = $wpdb->get_var("SELECT COUNT(id) as total FROM `".$wpdb->prefix."fundamental_livros_reservados` WHERE `livro_id`=".$livro->livro_id." AND `status`!=2");
					$html .= '<tr>';
					$html .= '<td>'.$livro->post_title.'</td>';
					$html .= '<td>'.$livro->quantidade_reservada.'</td>';
					$html .= '<td>'.date("d/m/Y", strtotime($livro->data_de_reserva)).'</td>';
					$html .= '<td>'.date("d/m/Y", strtotime($livro->data_de_entrega)).'</td>';
					$html .= '<td>'.(($qtd_cadastrada >= $qtd_reservada) ? "<a href=\"#\" onclick=\"estenderRetirada($livro->id)\" class=\"btn btn-info\">Estender</a>" : "<a href=\"#\" class=\"btn btn-info\" disabled=\"disabled\" data-toggle=\"tooltip\" title=\"Essa ação nao esta disponivel no momento, pois há pessoas que estão aguardando a retirada desde exemplar\">Estender</a>" ).'</td>';
					$html .= '</tr>';
				}

				$html .= '</tbody>';
				$html .= '</table>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}else{
				echo '<script type="text/javascript">window.location.replace("'.site_url().'/consultar-perfil/");</script>';
			}

		}else{
			return "<p>Não há livros reservados para esse CPF</p>";
		}
	}else{
		$html .= '<div class="container-fluid">';
			$html .= '<div class="row">';
				$html .= '<div class="col-xs-12 col-md-8 col-md-offset-2">';
					$html .= '<form name="consultarPerfilLivros" id="consultarPerfilLivros" action="" method="POST">';
						$html .= '<div class="form-group">';
							$html .= '<label for="cpfLivroUsuario">Por favor digite seu CPF (sem pontos)</label>';
							$html .= '<input type="text" name="cpfLivroUsuario" class="form-control" id="cpfLivroUsuario" placeholder="Digite seu CPF"/>';
						$html .= '</div>';
						$html .= '<div class="form-group">';
							$html .= '<button type="submit" name="entrarBtn" class="btn btn-success btn-block" id="entrarBtn" onclick="javascript: jQuery(this).html(\'Aguarde...\').attr(\'disabled\', true);jQuery(\'#consultarPerfilLivros\').submit();">Entrar</button>';
						$html .= '</div>';
					$html .= '</form>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';
	}
	return $html;
}
add_shortcode("fundamental_livros_perfil", "func_fundamental_livros_perfil");

/**
*
* Registra o post type Livros
*
*/
function post_type_livro_init(){
$labels = array(
		"name"               => "Livros",
		"singular_name"      => "Livro",
		"menu_name"          => "Livros",
		"name_admin_bar"     => "Livros",
		"add_new"            => "Adicionar novo",
		"add_new_item"       => "Adicionar novo Livro",
		"new_item"           => "Novo Livro",
		"edit_item"          => "Editar Livros",
		"view_item"          => "Ver Livro",
		"all_items"          => "Todos os Livros",
		"search_items"       => "Pesquisar Livros",
		"parent_item_colon"  => "Livros pai",
		"not_found"          => "Não foram encontrados Livros",
		"not_found_in_trash" => "Não há Livros na lixeira."
	);
$args = array(
		"labels"             => $labels,
        "description"        => "Registrar livros",
		"public"             => true,
		"publicly_queryable" => true,
		"show_ui"            => true,
		"show_in_menu"       => true,
		"query_var"          => true,
		"rewrite"            => array("slug" => "livro"),
		"capability_type"    => "post",
		"has_archive"        => true,
		"hierarchical"       => false,
		"menu_position"      => null,
		"supports"           => array('title','editor','thumbnail')
	);
    register_post_type("livro", $args );
}
add_action( 'init', 'post_type_livro_init' );

//Renomeia a caixa de featured image
add_action('do_meta_boxes', 'replace_featured_image_box');
function replace_featured_image_box()
{
	remove_meta_box( 'postimagediv', 'livro', 'side' );
	add_meta_box('postimagediv', "Capa do livro", 'post_thumbnail_meta_box', 'livro', 'side', 'low');
}
/*inserir_mais_conteudo*/

//Carrega scripts na area admin
function carregar_admin_scripts_gerenciador_de_biblioteca_fundamental($hook) {
    wp_enqueue_style("style_admin_gerenciador_de_biblioteca_fundamental", plugins_url("/css/style_backend.css", __FILE__));
    wp_enqueue_style("font_awesome_gerenciador_de_biblioteca_fundamental", plugins_url("/css/font-awesome.min.css", __FILE__));

    wp_enqueue_style("bootstrap_theme_gerenciador_de_biblioteca_fundamental", plugins_url("/css/bootstrap-theme.min.css", __FILE__));
    wp_enqueue_script("bootstrap_minjs_gerenciador_de_biblioteca_fundamental", plugins_url("/js/bootstrap.min.js", __FILE__), array("jquery"));
    wp_enqueue_style("bootstrap_min_gerenciador_de_biblioteca_fundamental", plugins_url("/css/bootstrap.min.css", __FILE__));
	wp_enqueue_script("ready_backendgerenciador_de_biblioteca_fundamental", plugins_url("/js/ready_backend.js", __FILE__), array("jquery"));
	wp_enqueue_script("mask_backend", plugins_url("/js/jquery.mask.min.js", __FILE__), array("jquery"));

	/*Inserir_script_admin*/
}
add_action("admin_enqueue_scripts", "carregar_admin_scripts_gerenciador_de_biblioteca_fundamental");

//Carrega scripts no frontend
function carregar_frontend_scripts_gerenciador_de_biblioteca_fundamental() {
	wp_enqueue_script("colorbox_js_fundamental_livro", plugins_url("/js/jquery.colorbox-min.js", __FILE__), array("jquery","ready_frontend"));
	wp_enqueue_style("style_frontend_gerenciador_de_biblioteca_fundamental", plugins_url("/css/style_frontend.css", __FILE__));
	/*Inserir_script_frontend*/
	wp_enqueue_script("bootstrap_minjs_gerenciador_de_biblioteca_fundamental_frontend", plugins_url("/js/bootstrap.min.js", __FILE__), array("jquery"));
	wp_enqueue_style("bootstrap_min_gerenciador_de_biblioteca_fundamental_frontend", plugins_url("/css/bootstrap.min.css", __FILE__));

	wp_enqueue_style("colorbox_css_fundamental_livros", plugins_url("/css/colorbox.css", __FILE__));
	wp_enqueue_script("mask_frontend", plugins_url("/js/jquery.mask.min.js", __FILE__), array("jquery"));
	wp_enqueue_script("ready_frontend", plugins_url("/js/ready_frontend.js", __FILE__), array("jquery"));
}


add_action("wp_enqueue_scripts", "carregar_frontend_scripts_gerenciador_de_biblioteca_fundamental");


/**
* Insere um menu
*/

function addMenu_gerenciador_de_biblioteca_fundamental() {
    add_menu_page("Gerenciar biblioteca","Gerenciar biblioteca","manage_options","menu_gerenciador_de_biblioteca_fundamental","func_dashboard_gerenciador_de_biblioteca_fundamental",plugins_url("gerenciador_de_biblioteca_fundamental/img/1486498943589a2c7f0bbcapng.png"));
}
add_action("admin_menu", "addMenu_gerenciador_de_biblioteca_fundamental");
//Tela admin do plugin
function func_dashboard_gerenciador_de_biblioteca_fundamental(){ global $wpdb; ?>
<div class="wrap">
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#aguardandoRetirada">Aguardando retirada</a></li>
		<li><a data-toggle="tab" href="#retiradas">Retiradas confirmadas</a></li>
		<li><a data-toggle="tab" href="#configuracoes">Configurações</a></li>
		<li><a data-toggle="tab" href="#ajuda">Ajuda</a></li>
	</ul>

	<div class="tab-content">
		<div id="aguardandoRetirada" class="tab-pane fade in active">
			<?php
			$aguardando = $wpdb->get_results("SELECT l.nome_reservista, l.email_reservista,l.cpf_Reservista,l.data_de_reserva,l.id, l.reserva_imediata, p.post_title FROM `".$wpdb->prefix."fundamental_livros_reservados` as l JOIN `".$wpdb->prefix."posts` as p  ON l.livro_id = p.ID WHERE l.status = 0 ORDER BY `data_de_reserva` ASC");
			?>
			<table class="table table-striped">
				<thead>
				<tr>
					<td>Nome do livro</td>
					<td>Nome do usuário</td>
					<td>E-mail</td>
					<td>CPF</td>
					<td>Data da reserva</td>
					<td>Status</td>
					<td>Lista De Espera</td>
					<td>Opções</td>
				</tr>
				</thead>
				<tbody>
				<?php
				if(!empty($aguardando)){
					$variavel = 0;
					foreach($aguardando as $aguardar){
						if($aguardar->reserva_imediata == "nao"){
							$variavel++;
						}
						echo '<tr id="'.$aguardar->id.'">
							<td>'.$aguardar->post_title.'</td>
							<td>'.$aguardar->nome_reservista.'</td>
							<td>'.$aguardar->email_reservista.'</td>
							<td>'.$aguardar->cpf_Reservista.'</td>
							<td>'.date("d/m/Y h:i:s", strtotime($aguardar->data_de_reserva)).'</td>
							<td>'.(($aguardar->reserva_imediata == "sim") ? "Aguardando confirmação" : "Aguardando disponibilidade").'</td>
							<td style="text-align: center">'.(($aguardar->reserva_imediata == "nao") ? "$variavel º": "---").'</td>
							<td><a href="javascript:void(0)" data-toggle="tooltip" title="Confirmar a retirada do livro" onclick="liberarRetirada('.$aguardar->id.', \''.$aguardar->reserva_imediata.'\')" class="btnLiberarRetirada"><i class="fa fa-edit"></i></a>
                            <button type="button" data-toggle="tooltip" style="background: #F44336;color: #ffffff;height: 29px;width: 24px;padding: 7px;border-radius: 4px;" title="Excluir pedido" onclick="removerEntrada('.$aguardar->id.', this)" class="btnRemoverEntrada">x</button>
                            </td>
						</tr>';
					}
				}else{
					echo '<tr>
						<td colspan="8" style="text-align: center;">No momento, não há solicitações de retirada.</td>
					</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div id="retiradas" class="tab-pane fade in">
			<?php
				$retiradas = $wpdb->get_results("SELECT l.nome_reservista, l.email_reservista, l.data_de_reserva,l.id, p.post_title FROM `".$wpdb->prefix."fundamental_livros_reservados` as l JOIN `".$wpdb->prefix."posts` as p  ON l.livro_id = p.ID WHERE l.status = 1");
			?>
			<table class="table table-striped">
				<thead>
					<tr>
						<td>Nome do livro</td>
						<td>Nome do usuário</td>
						<td>E-mail</td>
						<td>Data da reserva</td>
						<td>Opções</td>
					</tr>
				</thead>
				<tbody>
					<?php
					if(!empty($retiradas)){
						foreach($retiradas as $retirada){
							echo '<tr>
								<td>'.$retirada->post_title.'</td>
								<td>'.$retirada->nome_reservista.'</td>
								<td>'.$retirada->email_reservista.'</td>
								<td>'.date("d/m/Y h:i:s", strtotime($retirada->data_de_reserva)).'</td>
								<td><a href="javascript:void(0)" data-toggle="tooltip" title="Confirmar recebimento do livro" onclick="confirmarRecebimento('.$retirada->id.')" class="btnAdminEditar"><i class="fa fa-edit"></i></a></td>
							</tr>';
						}
					}else{
						echo '<tr>
							<td colspan="5" style="text-align: center">No momento, não há retiradas confirmadas.</td>
						</tr>';
					}
					?>
				</tbody>
			</table>
			<!--  Modal para dar baixa na retirada de livro-->
			<!-- Modal -->
			<div id="modalConfirmar" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Confirmar Recebimento</h4>
						</div>
						<div class="modal-body">
							<div class="modalLoading" style="padding: 30px 0">
								<p style="margin: 0;text-align: center">
									<img src="<?php echo plugin_dir_url(__FILE__); ?>/img/ajax-loader.gif" />
								</p>
							</div>
							<div class="modalConteudoConfirmacao" style="display: none">

							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						</div>
					</div>

				</div>
			</div>
			<!-- #Modal para dar baixa na retirada de livro-->
		</div>
		<div id="configuracoes" class="tab-pane fade in">
			<br/>
			<script>
				jQuery(document).ready(function(){
					jQuery('.valorMulta').mask('000.000.000.000.000,00', {reverse: true});
				});
				//Salva as configuracoes informadas (multa, e dias)
				function salvarConfiguracoesPlugin(){
					jQuery("#btnSalvarConfiguracoes").html("Aguarde...").attr("disabled", true);
					jQuery.ajax({
						type: 'POST',
						<?php echo 'url: "'.plugin_dir_url(__FILE__).'php/carregarLivros.php",'; ?>
						data: {
							Acao: "salvarConfiguracoesPlugin",
							Dias: jQuery("#qtdDias").val(),
							Multa: jQuery("#valorMulta").val()
						}
					}).done(function(retConfigs){
						if(retConfigs == "OK"){
							window.location.reload();
						}
					});
				}
			</script>
			<?php

			?>
			<div class="form-group">
				<label>Quantos dias o usuário pode ficar com o livro? <span style="font-size: 13px;color: #b3b3b3;">(Ex.: 7 dias contados a partir da data de retirada)</span></label>
				<p><input type="number" min="1" name="qtdDias" value="<?php echo $wpdb->get_var("SELECT `config_value` FROM `".$wpdb->prefix."fundamental_livros_config` WHERE `config_name`= 'tempo_posse'"); ?>" id="qtdDias"/> <span>dias</span></p>
			</div>
			<div class="form-group">
				<label>Qual o valor da multa <strong>por dia?</strong> <span style="font-size: 13px;color: #b3b3b3;">(Ex.: 1,50)</span></label>
				<p><span>R$</span> <input type="text" class="valorMulta" name="valorMulta" id="valorMulta" min="1" value="<?php echo $wpdb->get_var("SELECT `config_value` FROM `".$wpdb->prefix."fundamental_livros_config` WHERE `config_name`= 'multa_diaria_por_atraso'"); ?>"/></p>
			</div>
			<div class="form-group">
				<button class="btn btn-success" id="btnSalvarConfiguracoes" onclick="salvarConfiguracoesPlugin()">Salvar Configurações</button>
				<button class="btn btrn-default" onclick="javascript:window.location.reload()">Cancelar</button>
			</div>
		</div>
		<div id="ajuda" class="tab-pane fade in">
			<h3>Como inserir a busca de livros no site?</h3>
			<p>Basta colocar o shortcode: [fundamental_livros] na página onde você deseja que a mesma apareça.</p>
			<br/>
			<h3>Como criar uma página para o usuário acessar seu painel?</h3>
			<p>Basta colocar o shortcode: [fundamental_livros_perfil] na página onde você deseja que o acesso apareça.</p>
			<br/>
		</div>
	</div>
	<!-- Modal confirmar liberação do livro-->
	<div id="modalLiberar" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Confirmar Liberação</h4>
				</div>
				<div class="modal-body">
					<p id="clienteReservouLivro">Essa ação irá confirmar que o usuário recebeu o livro. E os dias começarão a serem contados a partir dessa data.<br/>Deseja confirmar?</p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="idRetiradaaConfirmar" id="idRetiradaaConfirmar" value="x"/>
					<button type="button" onclick="confirmarRetiradaAgora()" class="btn btn-success">Confirmar</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>

		</div>
	</div>
	<!-- #Modal para confirmar o recebimento do livro -->
	<?php
		echo '<script type="text/javascript">
		function confirmarRecebimento(reserva_id){
			jQuery("#modalConfirmar").modal({"backdrop":"static", "keyboard":false});
			jQuery("#modalConfirmar").modal("show");
			jQuery.ajax({
				type: "POST",
				url: "'.plugin_dir_url(__FILE__).'php/carregarLivros.php",
				data: {
					Acao: "confirmarEntregLivro",
					reserva: reserva_id
				}
			}).done(function(ret){
				jQuery(".modalLoading").hide();
				jQuery(".modalConteudoConfirmacao").html(ret);
				jQuery(".modalConteudoConfirmacao").show();
			});
		}
		//Da baixa no livro/Confirma o recebimento
		function darBaixa(reserva_id, btnConfirmacao){
			jQuery(btnConfirmacao).html("Aguarde...").attr("disabled", true);
			jQuery("#containerBotaoConfirmarRecebimento").append(\'<p style="font-size:12px;text-align:center;margin-top:5px;">Por favor aguarde, você será redirecionado quando terminar.</p>\');
			jQuery.ajax({
				type: "POST",
				url: "'.plugin_dir_url(__FILE__).'php/carregarLivros.php",
				data: {
					Acao: "liberarLivro",
					reserva: reserva_id
				}
			}).done(function(ret){
				window.location.reload();
			});
		}

		//Libera uma retirada
		function liberarRetirada(retirada_id, reserva_imediata){
		jQuery("#modalLiberar .modal-body div").remove("#clienteAguardaEstoque");
		jQuery("#modalLiberar .modal-footer input").remove("#reservaImediataNAO");
		jQuery("#modalLiberar .modal-body p#clienteReservouLivro").show();
			jQuery("#idRetiradaaConfirmar").val(retirada_id);
			if(reserva_imediata == "nao"){
				jQuery("#modalLiberar .modal-body p#clienteReservouLivro").hide();
				jQuery("#modalLiberar .modal-body").append("<div id=\'clienteAguardaEstoque\' class=\'clienteAguardaEstoque\'><p style=\'line-height: 20px; font-weight: 600; margin-top: 10px\'>Cliente esta aguardando estoque para retirar o livro!</p><p>Se há estoque por favor confirme a reserva clicando em <strong>Confirmar</strong></p></div>");
				jQuery("#modalLiberar .modal-footer").append("<input type=\'hidden\' id=\'reservaImediataNAO\' value=\'nao\'/>");

			}else{
				//nada a fazer
			}
			jQuery("#modalLiberar").modal({"backdrop":"static", "keyboard":false});
			jQuery("#modalLiberar").modal("show");
		}

		//AJAX pra confirmar retirada do livro
		function confirmarRetiradaAgora(){
			retirada_id_param = jQuery("#idRetiradaaConfirmar").val();
			reserva_imediata = (jQuery("#modalLiberar .modal-footer input#reservaImediataNAO").val()) == "nao" ? "nao" : "sim";
			jQuery.ajax({
				type: "POST",
				url: "'.plugin_dir_url(__FILE__).'php/carregarLivros.php",
				data: {
					Acao: "entregarLivro",
					reserva: retirada_id_param,
					reserva_imediata: (reserva_imediata == "nao") ? "nao" : "sim"
				}
			}).done(function(ret){
				window.location.reload();
				//console.log(ret);
			});
		}
        //Remove a linha solicitada
        function removerEntrada(linha, obj){
            jQuery(obj).attr("disabled", true);
            jQuery.ajax({
				type: "POST",
				url: "'.plugin_dir_url(__FILE__).'php/carregarLivros.php",
				data: {
					Acao: "excluirEntrada",
					Entrada: linha
				}
			}).done(function(ret){
				jQuery("tr#"+linha).hide("fast", function(){
                    jQuery(this).remove();
                    window.location.reload();
                });
			});


        }
	</script>';
	?>
</div><?php }
?>
