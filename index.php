<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feed_url'])) {
    $feed_url = $_POST['feed_url'];
    if (!empty($feed_url)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO feeds (url) VALUES (:url)");
        $stmt->bindParam(':url', $feed_url);
        if ($stmt->execute()) {
            $message = "Feed agregado correctamente.";
        } else {
            $message = "Error al agregar el feed.";
        }
    } else {
        $message = "La URL no puede estar vacía.";
    }
}

$order = isset($_GET['order']) ? $_GET['order'] : 'pub_date DESC';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT news.*, feeds.url AS feed_url FROM news JOIN feeds ON news.feed_id = feeds.id";
if ($search) {
    $sql .= " WHERE title LIKE :search OR description LIKE :search";
}
$sql .= " ORDER BY $order";

$stmt = $conn->prepare($sql);
if ($search) {
    $stmt->bindValue(':search', "%$search%");
}
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El mejor Lector RSS de la vida</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-container">
        <!-- Sección de imágenes -->
        <div class="image-gallery">
            <img src="images/SoraAlejandro.jpg" alt="Su main en Smash">
            <img src="images/WargreymonGildardo.jpg" alt="su Digimon favorito">
            <img src="images/RoaringMuñoz.jpg" alt="Su deck">
        </div>
        
        <!-- Cabecera con título -->
        <header class="page-header">
            <h1>El mejor lector RSS de la vida, para videojuegos y tecnología</h1>
        </header>

        <!-- Contenedor principal de contenido -->
        <div class="content-wrapper">
            <!-- Sección de formularios -->
            <div class="form-section">
                <form action="index.php" method="post">
                    <input type="text" name="feed_url" placeholder="Ingresa la URL del feed" required>
                    <button type="submit">Agregar Feed</button>
                </form>
                
                <?php if (isset($message)): ?>
                    <div class="alert-message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="get" class="search-form">
                    <input type="text" name="search" placeholder="Buscar noticias..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Buscar</button>
                </form>
            </div>

            <!-- Botón de actualización -->
            <div class="action-buttons">
                <button onclick="window.location.href='update_feeds.php'">Actualizar Feeds</button>
            </div>

            <!-- Tabla de noticias -->
            <div class="table-container">
                <table class="news-table">
                    <thead>
                        <tr>
                            <th><a href="index.php?order=title">Título</a></th>
                            <th><a href="index.php?order=description">Descripción</a></th>
                            <th><a href="index.php?order=categories">Categorías</a></th>
                            <th><a href="index.php?order=pub_date">Fecha</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news as $item): ?>
                        <tr>
                            <td><a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['title']); ?></a></td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><?php echo htmlspecialchars($item['categories']); ?></td>
                            <td><?php echo htmlspecialchars($item['pub_date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>