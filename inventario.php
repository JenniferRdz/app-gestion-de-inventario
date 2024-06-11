<?php
// Configuración de la conexión
$serverName = "localhost"; 
$database = "InventoryDB"; 
$dsn = "sqlsrv:server=$serverName;Database=$database";

// Opciones de conexión
$options = [
    PDO::SQLSRV_ATTR_DIRECT_QUERY => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
];

try {
    // Crear la conexión PDO utilizando la autenticación integrada de Windows
    $conn = new PDO($dsn, null, null, $options);
    echo "Conexión exitosa!<br>";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función para agregar un producto
function addProduct($conn, $name, $description, $price, $quantity) {
    try {
        // Preparar la consulta SQL
        $sql = "INSERT INTO Inventory (ProductName, Description, Price, Quantity) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Ejecutar la consulta con los valores proporcionados
        $stmt->execute([$name, $description, $price, $quantity]);

        echo "Producto agregado exitosamente!";
    } catch (PDOException $e) {
        // Manejar errores de consulta
        die("Error al agregar producto: " . $e->getMessage());
    }
}

// 


function getInventory($conn) {
    try {
        // Preparar la consulta SQL
        $sql = "SELECT ProductName, Description, Price, Quantity FROM Inventory";
        $stmt = $conn->query($sql);
        // Obtener resultados como un array asociativo
        $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $inventory;
    } catch (PDOException $e) {
        // Manejar errores de consulta
        die("Error al obtener inventario: " . $e->getMessage());
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];
    $productQuantity = $_POST['productQuantity'];

    // Agregar el producto al inventario
    addProduct($conn, $productName, $productDescription, $productPrice, $productQuantity);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Gestión de Inventario</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="productName" class="form-label">Nombre del Producto:</label>
                <input type="text" class="form-control" id="productName" name="productName" required>
            </div>
            <div class="mb-3">
                <label for="productDescription" class="form-label">Descripción del Producto:</label>
                <input type="text" class="form-control" id="productDescription" name="productDescription" required>
            </div>
            <div class="mb-3">
                <label for="productPrice" class="form-label">Precio del Producto:</label>
                <input type="number" class="form-control" id="productPrice" name="productPrice" min="0.01" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="productQuantity" class="form-label">Cantidad del Producto:</label>
                <input type="number" class="form-control" id="productQuantity" name="productQuantity" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Producto</button>
        </form>

        <h2 class="mt-5">Inventario Actual</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Obtener los productos del inventario y mostrarlos en una tabla
                $inventory = getInventory($conn);
                foreach ($inventory as $product) {
                    echo "<tr>";
                    echo "<td>{$product['ProductName']}</td>";
                    echo "<td>{$product['Description']}</td>";
                    echo "<td>{$product['Price']}</td>";
                    echo "<td>{$product['Quantity']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn = null;
?>
