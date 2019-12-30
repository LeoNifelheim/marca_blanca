<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
	//Lectura del archivo de afiliados para comparar con url y carga del config.php adecuado.
	$affilitesFile = './affiliates.json';
	$affiliates = file_get_contents($affilitesFile);
	$affiliates = json_decode($affiliates);
	
	foreach ($affiliates as $affiliate) {
		if ( $_SERVER['HTTP_HOST'] == $affiliate->url){
			require('./'.$affiliate->url.'/config.php');
		}
	}
	//Clear de la clase $affiliates
	unset($affiliate)
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title></title>

<!-- Star CSS and Javascript -->
		<link rel="stylesheet" href="<?php echo $css;?>reset.css" type="text/css" media="screen,projection">
		<link rel="stylesheet" href="<?php echo $css;?>estilos.css" type="text/css" media="screen,projection">
<!-- end CSS and Javascript -->

		<title><?php echo $affiliate;?></title>
		<script src="<?php echo $js;?>jquery.js"></script>
		<script src="<?php echo $js;?>jquery.simplemodal.js"></script>		
		
	</head>
	<body>
		<?php require($templates.'header.php');?>
		<!-- termina HEADER -->
		<div class="listado-chicas">
<?php

	//Lectura del json recibido por la API
	$json=file_get_contents($urlAPI);
	$json=json_decode($json);
	
	//Declaración de variables extra
	$k = 0;
	$j = False;
	$aux = 1;
	
	$nImgP = 0;
	$nImgG = 0;
	
	//Lectura y almacenaje de las imagenes
	foreach (new DirectoryIterator($images) as $fileInfo) {
		if($fileInfo->isDot()) continue;
		$a = $fileInfo->getFilename();
		if (strpos($a, 'thumb-grande') !== false) {
			$imgSrcG[$nImgG] = $fileInfo->getFilename();
			$nImgG++;
		}else{
			$imgSrcP[$nImgP] = $fileInfo->getFilename();
			$nImgP++;
		}
	}
	//Reset de la posición del array para aseugar el comienzo desde 0
	reset($imgSrcP);
	reset($imgSrcG);
	
	//Separación top 5 thumb del json con el resto
	$thumbTop = array_slice($json, 0, 5);
	$thumbRest = array_slice($json, 5);
	
	//Recorrido de los resultados del json y muestreo
	foreach ($thumbRest as $value) {
		
		$urlToPass = $url.$value->wbmerPermalink."/?nats=".$trackingCode;
		
		//Comprobación de reset del array de imagenes, para no repetir imagenes pequeñas
		if ( !current($imgSrcP) ){
			reset($imgSrcP);
			$imgSrc = $images.'/'.$imgSrcP[0];
			next($imgSrcP);
		}else{
			$imgSrc = $images.'/'.current($imgSrcP);
			next($imgSrcP);
		}
		
		//Lógica de muestreo de las imagenes, cada 5, la sexta es grande
		if ( $k == 5 ){
			//Comprobación de reset del array de imagenes, para no repetir imagenes grandes
			if ( !current($imgSrcG) ){
				reset($imgSrcG);
				$imgSrc2 = $images.'/'.$imgSrcG[0];
				next($imgSrcG);	
			}else{
				$imgSrc2 = $images.'/'.current($imgSrcG);
				next($imgSrcG);				
			}
			
			$urlToPass = $url.$thumbTop[$aux-1]->wbmerPermalink."/?nats=".$trackingCode;
			//Lógica para mostrar la imagen a la derecha o a la izquierda de forma intermitente
			if ( $j ){
				$j = False;
			?>
				<div class="chica chica-grande grande-derecha">
			
			<?php
			}else{
				$j = True;
			?>	
				<div class="chica chica-grande">
			<?php
			}		
			//Muestra de las imagenes grandes con sus links
			?>
				<a class="link" href="<?php echo $urlToPass;?>" title="">
					<span class="thumb"><img src="<?php echo $imgSrc2;?>" width="175" height="150" alt="" title="" /></span>
					<span class="nombre-chica"> <span class="ico-online"></span> <?php echo $thumbTop[$aux-1]->wbmerNick;?></span>
					<span id="favorito" class="ico-favorito" ></span>
				</a>
			</div>
<?php
			$aux++;
		}elseif( $k >= 0 && $k <= 11 ){
			//Muestra de las imagenes pequeñas con sus links
			?>	
			<div class="chica">
				<a class="link" href="<?php echo $urlToPass;?>" title="">
					<span class="thumb"><img src="<?php echo $imgSrc;?>" width="175" height="150" alt="" title="" /></span>
					<span class="nombre-chica"> <span class="ico-online"></span> <?php echo $value->wbmerNick;?></span>
					<span id="favorito" class="ico-favorito" ></span>
				</a>
			</div>
<?php
		}
		if ( $k == 11) { $k = -1; }
		$k++;
	}
	unset($value);
?>
			<div class="clear"></div>
			<a class="btn-mas-modelos" href="#" title="Mostrar más modelos">Siguiente Página</a>
		</div>
		<!-- termina LISTADO DE CHICAS -->
		<?php require($templates.'footer.php');?>
		<!-- termina MENU FOOTER -->
		<?php require($templates.'copy.php');?>
		<!-- termina COPY -->
		<?php require($templates.'data.php');?>
		<!-- termina DATA -->
	</body>
	<script>
	//Carga del iframe en modal
		$(document).on("click","a.link",function (e) {
			e.preventDefault();

			var src = $(this).attr("href");
			console.log(src)
			
			$.modal('<iframe src="' + src + '" height="680" width="980" style="border:0">', {
				closeHTML:"",
				containerCss:{
					backgroundColor:"#fff", 
					borderColor:"#fff", 
					height:690, 
					padding:0, 
					width:990
				},
				overlayClose:true
			});
		});
	</script>
</html>