<?php
$k1='db.php';
if(!file_exists($k1)) die("Error: Archivo db.php no encontrado");
include$k1;
if(!isset($conn)) die("Error: Conexión no establecida en db.php");;$v2=$_SERVER;$v3='REQUEST_METHOD';
if($v2[$v3]==='POST'&&isset($_POST['feed_url'])){
$v4=$_POST['feed_url'];if(!empty($v4)){
$v5=$conn->prepare(base64_decode('SU5TRVJUIElHTk9SRSBJTlRPIGZlZWRzKHVybClWQUxVRVMoOnVybCk='));
$v5->bindParam(':url',$v4);if($v5->execute()){$v6="Feed agregado correctamente.";
}else{$v6="Error al agregar el feed.";}}else{$v6="La URL no puede estar vacía.";}}
$v7=isset($_GET['order'])?$_GET['order']:'pub_date DESC';$v8=isset($_GET['search'])?$_GET['search']:'';
$v9=base64_decode('U0VMRUNUIG5ld3MuKixmZWVkcy51cmwgQVMgZmVlZF91cmwgRlJPTSBuZXdzIEpPSU4gZmVlZHMgT04gbmV3cy5mZWVkX2lkPWZlZWRzLmlk');
if($v8){$v9.=base64_decode('IFdIRVJFIHRpdGxlIExJS0U6c2VhcmNoIE9SIGRlc2NyaXB0aW9uIExJS0U6c2VhcmNo');}
$v9.=" ORDER BY $v7";$v5=$conn->prepare($v9);if($v8){$v5->bindValue(':search',"%$v8%");}
$v5->execute();$v10=$v5->fetchAll(PDO::FETCH_ASSOC);?>
<!DOCTYPE html><html lang=es><head><meta charset=UTF-8><meta name=viewport content="width=device-width,initial-scale=1">
<title>El mejor Lector RSS de la vida</title><link rel=stylesheet href=styles.css></head><body>
<div class=main-container><div class=image-gallery><img src=images/SoraAlejandro.jpg alt="Su main en Smash">
<img src=images/WargreymonGildardo.jpg alt="su Digimon favorito"><img src=images/RoaringMuñoz.jpg alt="Su deck">
</div><header class=page-header><h1>El mejor lector RSS de la vida, para videojuegos y tecnología</h1></header>
<div class=content-wrapper><div class=form-section><form action=index.php method=post>
<input type=text name=feed_url placeholder="Ingresa la URL del feed" required><button type=submit>Agregar Feed</button>
</form><?php if(isset($v6)):?><div class="alert-message <?php echo strpos($v6,'Error')!==false?'error':'success';?>">
<?php echo htmlspecialchars($v6);?></div><?php endif;?><form action=index.php method=get class=search-form>
<input type=text name=search placeholder="Buscar noticias..." value="<?php echo htmlspecialchars($v8);?>">
<button type=submit>Buscar</button></form></div><div class=action-buttons>
<button onclick="window.location.href='update_feeds.php'">Actualizar Feeds</button></div>
<div class=table-container><table class=news-table><thead><tr><th><a href="index.php?order=title">Título</a></th>
<th><a href="index.php?order=description">Descripción</a></th><th><a href="index.php?order=categories">Categorías</a></th>
<th><a href="index.php?order=pub_date">Fecha</a></th></tr></thead><tbody><?php foreach($v10 as $v11):?><tr>
<td><a href="<?php echo htmlspecialchars($v11['url']);?>"><?php echo htmlspecialchars($v11['title']);?></a></td>
<td><?php echo htmlspecialchars($v11['description']);?></td><td><?php echo htmlspecialchars($v11['categories']);?></td>
<td><?php echo htmlspecialchars($v11['pub_date']);?></td></tr><?php endforeach;?></tbody></table></div></div></div></body></html>