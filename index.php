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
<head>
    <meta charset="UTF-8">
    
    <title>El mejor Lector RSS de la vida</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #2c3e50;
        }

        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 100%;
            max-width: 500px;
        }

        input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            max-width: 800px;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ecf0f1;
        }

        a {
            text-decoration: none;
            color: #3498db;
        }

        a:hover {
            text-decoration: underline;
        }

        .contenedor {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr; 
            gap: 10px; 
            align-items: center; 
        }
        .contenedor img {
            width: 100%; 
            height: auto; 
        }

        .contenedor-con-fondo {
    background-image: url('images/fondo.avif');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh; 
    display: flex;
    justify-content: center;
    align-items: center;
    color: white; 
    text-align: center;
    display: flex;
    flex-direction: column; 
    gap: 20px; 
}

.contenedor-con-fondo-titulo {
    background-image: url('images/backgroundtitulo.webp');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh; 
    display: flex;
    justify-content: center;
    align-items: center;
    color: white; 
    text-align: center;
}

body {
    background-image: url('images/background.jpg'); 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
    margin: 0; 
    height: 100vh; 
}

    </style>
</head>
<body>

    <div class="contenedor">
        <img src="images\SoraAlejandro.jpg" alt="Su main en Smash" style="width: 300px; height: 300px;">
        
        <img src="images\WargreymonGildardo.jpg" alt="su Digimon favorito" style="width: 300px; height: 300px;">
        
        <img src="images\RoaringMuñoz.jpg" alt="Su deck" style="width: 300px; height: 300px;">
        
    </div> 
    
    <div class="contenedor-con-fondo-titulo">
    <h1 style="color: white;";"">El mejor lector RSS de la vida, para videojuegos y tecnología</h1>
    </div>

    <div class="contenedor-con-fondo">
    <form action="index.php" method="post">
        <input type="text" name="feed_url" placeholder="Ingresa la URL del feed" required>
        <button type="submit">Agregar Feed</button>
    </form>
    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="index.php" method="get">
        <input type="text" name="search" placeholder="Buscar noticias..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Buscar</button>
    </form>
    </div>

    <button onclick="window.location.href='update_feeds.php'">Actualizar Feeds</button>

    <table>
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
</body>
</html>