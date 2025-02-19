<?php
include 'db.php';

// Obtener todas las URLs de los feeds
$stmt = $conn->query("SELECT id, url FROM feeds");
$feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($feeds as $feed) {
    $feed_id = $feed['id'];
    $url = $feed['url'];

    // Evitar que los errores de XML se muestren en pantalla
    libxml_use_internal_errors(true);

    // Intentar cargar el feed RSS
    $rss = simplexml_load_file($url);
    if ($rss === false) {
        // Mostrar un mensaje de error en caso de fallo
        echo "Error al cargar el feed: $url<br>";
        libxml_clear_errors(); // Limpiar los errores de XML
        continue; // Saltar al siguiente feed
    }

    // Guardar cada noticia en la base de datos
    foreach ($rss->channel->item as $item) {
        $title = (string)$item->title;
        $link = (string)$item->link;
        $description = (string)$item->description;
        $categories = implode(', ', (array)$item->category);
        $pub_date = date('Y-m-d H:i:s', strtotime((string)$item->pubDate));

        try {
            // Verificar si la noticia ya existe
            $stmt = $conn->prepare("SELECT id FROM news WHERE title = :title AND url = :url");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':url', $link);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                // Insertar la noticia si no existe
                $stmt = $conn->prepare("INSERT INTO news (feed_id, title, url, description, categories, pub_date) VALUES (:feed_id, :title, :url, :description, :categories, :pub_date)");
                $stmt->bindParam(':feed_id', $feed_id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':url', $link);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':categories', $categories);
                $stmt->bindParam(':pub_date', $pub_date);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo "Error al guardar la noticia: " . $e->getMessage() . "<br>";
        }
    }
}

header('Location: index.php');
?>