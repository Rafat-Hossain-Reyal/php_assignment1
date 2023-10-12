<?php
$data = [];


if (file_exists('books.json')) {
    $data = json_decode(file_get_contents('books.json'), true);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['title']) && !empty($_POST['author'])) {
        $newBook = [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'available' => false, 
            'pages' => 0, 
            'isbn' => '', 
        ];

        
        if (!empty($_POST['available'])) {
            $newBook['available'] = (bool)$_POST['available'];
        }
        if (!empty($_POST['pages'])) {
            $newBook['pages'] = (int)$_POST['pages'];
        }
        if (!empty($_POST['isbn'])) {
            $newBook['isbn'] = $_POST['isbn'];
        }

        $data[] = $newBook;

        file_put_contents('books.json', json_encode($data, JSON_PRETTY_PRINT));

        header('Location: index.php');
        exit;
    }
}


if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    if (isset($data[$index])) {
        unset($data[$index]);
        $data = array_values($data); 
        file_put_contents('books.json', json_encode($data, JSON_PRETTY_PRINT));
        header('Location: index.php');
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchTerm = strtolower($_GET['search']);
    $results = [];
    foreach ($data as $index => $book) {
        $bookTitle = strtolower($book['title']);
        
        $titleWords = explode(' ', $bookTitle);
        
        if (strpos($titleWords[0], $searchTerm) === 0) {
            $results[] = ['title' => $book['title'], 'author' => $book['author'], 'available' => $book['available'],'pages' => $book['pages'],'isbn' => $book['isbn'],'index' => $index];
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Book store</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Book Library</h1>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <label for="author">Author:</label>
        <input type="text" name="author" id="author" required>
        <label for="available">Available:</label>
        <input type="checkbox" name="available" id="available" value="1">
        <label for="pages">Pages:</label>
        <input type="number" name="pages" id="pages" min="0">
        <label for="isbn">ISBN:</label>
        <input type="text" name="isbn" id="isbn">
        <button type="submit">Add Book</button>
    </form>

    <h2>Search Books</h2>
    <form method="GET">
        <label for="search">Search:</label>
        <input type="text" name="search" id="search" placeholder="Search for books">
        <button type="submit">Search</button>
    </form>

    <?php if (isset($noResultsMessage)) {
        echo "<p>$noResultsMessage</p>";
    } ?>

    <h2>Books</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Available</th>
            <th>Pages</th>
            <th>ISBN</th>
            <th>Action</th>
        </tr>
        <?php foreach ($data as $index => $book) { ?>
            <tr>
                <td><?= $book['title'] ?></td>
                <td><?= $book['author'] ?></td>
                <td><?= $book['available'] ? 'Yes' : 'No' ?></td>
                <td><?= $book['pages'] ?></td>
                <td><?= $book['isbn'] ?></td>
                <td>
                    <a href="?delete=<?= $index ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php if (isset($results)) {
        echo "<h2>Search Results</h2>";
        if (empty($results)) {
            echo "No results found.";
        } else {
            echo "<table>";
            echo "<tr><th>Title</th><th>Author</th><th>Available</th><th>Pages</th><th>ISBN</th></tr>";
            foreach ($results as $book) {
                echo "<tr>";
                echo "<td>{$book['title']}</td>";
                echo "<td>{$book['author']}</td>";
                echo "<td>{$book['available']}</td>";
                echo "<td>{$book['pages']}</td>";
                echo "<td>{$book['isbn']}</td>";
                //echo "<td><a href='?delete={$book['index']}'>Delete</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } ?>
</body>
</html>
