<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "facturacion");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si el usuario está autenticado
if (isset($_SESSION['Usuario'])) {
    // Obtener la hora actual
    $tiempo_actual = time();

    // Verificar si la sesión ha estado inactiva por más de 14 minutos
    if (isset($_SESSION['ultima_actividad']) && ($tiempo_actual - $_SESSION['ultima_actividad'] > 840)) {
        session_unset();
        session_destroy();
        echo '<script>alert("Tu sesión ha estado inactiva. Serás redirigido a la página de inicio de sesión."); window.location = "../usuario/registro_cliente.php";</script>';
        exit;
    }

    // Actualizar el tiempo de la última actividad
    $_SESSION['ultima_actividad'] = $tiempo_actual;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S&S Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../usuario/CSS/estilo4.css">
    <link rel="stylesheet" href="../usuario/CSS/estilo5.css">                                   
    <link rel="stylesheet" href="../usuario/CSS/estilo6.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-rOA1CKM2Q3s9pJFjT+M+zZT1J/int2ZUWphGfO2NUA8Gb0/g3X/+X9haS6dXx/k+" crossorigin="anonymous">
    <link rel="stylesheet" href="../usuario/CSS/estilo7.css">
    <link rel="stylesheet" href="../usuario/CSS/estilo8.css">
</head>
<body>
    <header>
        <div class="header-superior">
            <div class="titulo">
                <h1 class="text-center">S&S STORE COSITAS HECHAS A MANO</h1>
            </div>
            <div class="usuario-menu" style="<?php echo isset($_SESSION['Usuario']) ? '' : 'display: none;'; ?>">
                <div class="icono-usuario" id="nombreUsuarioDiv" onclick="mostrarMenuUsuario()">
                    <i class="fas fa-user"></i>
                    <span id="correoUsuario"><?php echo isset($_SESSION['Correo']) ? $_SESSION['Correo'] : ''; ?></span>
                </div>
                <div class="menu-usuario" id="menuUsuario">
        
                </div>
            </div>
        </div>
        <div class="container-menu">
            <div class="submenu">
                <input type="checkbox" id="check-menu">
                <label id="label-check" for="check-menu">
                    <i class="fa-solid fa-bars icon-menu"></i>
                </label>
                <nav>
                    <ul>
    
                        <li><a href="#"><i class="fi fi-rr-user"></i>Contactos</a>
                            <ul>
                                <li><a href="https://wa.me/0968381035">WhatsApp</a></li>
                                <li><a href="https://maps.app.goo.gl/tzAejV4WMMqkfshw6">Ubicación</a></li>
                            </ul>
                        </li>
                        <li><a href="#carritoBtn"><i class="fas fa-shopping-cart"></i> Carrito</a></li>
                        <li><a href="#">Salir</a>
                            <ul>
                                <li><a href="../administrador/salir.php" id="Cerrar"><i class="fa-solid fa-sign-out-alt"></i>Salir</a></li>
                                
                            </ul>
                        </li>
                
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="barra-lateral col-12 col-sm-auto order-sm-first">
            <nav class="menu">
                <a href="../usuario/clientes.php"><i class="fas fa-home"></i><span>Inicio</span></a>
                <a href="?categoria=Ropa"><i class="fas fa-tshirt"></i><span>Ropa</span></a>
                <a href="?categoria=bisuteria"><i class="fas fa-gem"></i><span>Bisutería</span></a>
                <a href="?categoria=amigurumis"><i class="fas fa-puzzle-piece"></i><span>Amigurumis</span></a>
                <a href="?categoria=llaveros"><i class="fas fa-key"></i><span>Llaveros</span></a>
            </nav>
        </div>
        <div id="mensajeFlotante" class="mensaje-flotante oculto"></div>
        <main class="main flex-sm-column">
            <a name="Inicio"></a>
            <section class="paintings">

<?php
    // Conexión a la base de datos (reemplaza con tus propios detalles)
    $conexion = new mysqli("localhost", "root", "", "facturacion");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Variable para almacenar la categoría seleccionada
    $categoriaSeleccionada = "";

    // Verificar si se ha seleccionado una categoría y limpiar/validar la entrada
    if (isset($_GET['categoria'])) {
        $categoriaSeleccionada = $_GET['categoria'];

        // Consultar el IdCateg correspondiente a la categoría seleccionada
        $sql_categoria = "SELECT IdCateg FROM categoria WHERE NombreCategoria = ?";
        $stmt_categoria = $conexion->prepare($sql_categoria);
        $stmt_categoria->bind_param("s", $categoriaSeleccionada);
        $stmt_categoria->execute();
        $result_categoria = $stmt_categoria->get_result();

        if ($result_categoria->num_rows > 0) {
            $row_categoria = $result_categoria->fetch_assoc();
            $idCategoria = $row_categoria['IdCateg'];

            // Consulta para obtener los productos de la categoría seleccionada por IdCateg
            $sql = "SELECT * FROM productos_artesanias WHERE IdCateg = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $idCategoria);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si hay productos disponibles en esta categoría
            if ($result->num_rows > 0) {
                // Mostrar los productos en la página principal
                while ($row = $result->fetch_assoc()) {
                    echo '<article>';
                    echo '<img class="imagen" src="../administrador/uploads/' . $row['imagen_path'] . '" alt="' . $row['nombre_artesanias'] . '">';
                    echo '<div class="informacion">';
                    echo '<h5>' . $row['nombre_artesanias'] . '</h5>';
                    echo '<p class="precio">Precio: $' . $row['precio'] . '</p>';
                    echo '<button onclick="agregarAlCarrito(\'' . $row['nombre_artesanias'] . '\', ' . $row['precio'] . ', \'' . $row['imagen_path'] . '\')">Agregar al Carrito</button>';
                    echo '</div>';
                    echo '</article>';
                }
            } else {
                // No hay productos disponibles para esta categoría
                echo "<script>mostrarMensaje('No hay productos disponibles en esta categoría.');</script>";
            }
        } else {
            // La categoría seleccionada no existe en la tabla de categorías
            echo "Categoría no encontrada.";
            exit; // Salir del script
        }
    } else {
        // Consulta para obtener todos los productos
        $sql_todos_los_productos = "SELECT * FROM productos_artesanias";
        $result_todos_los_productos = $conexion->query($sql_todos_los_productos);

        // Mostrar todos los productos en la página principal
        while ($row = $result_todos_los_productos->fetch_assoc()) {
            echo '<article>';
            echo '<img class="imagen" src="../administrador/uploads/' . $row['imagen_path'] . '" alt="' . $row['nombre_artesanias'] . '">';
            echo '<div class="informacion">';
            echo '<h5>' . $row['nombre_artesanias'] . '</h5>';
            echo '<p class="precio">Precio: $' . $row['precio'] . '</p>';
            echo '<button onclick="agregarAlCarrito(\'' . $row['nombre_artesanias'] . '\', ' . $row['precio'] . ', \'' . $row['imagen_path'] . '\')">Agregar al Carrito</button>';
            echo '</div>';
            echo '</article>';
        }
    }

    // Cerrar la conexión
    $conexion->close();
?>
                <script>
                    function mostrarMensaje(mensaje) {
                        var mensajeFlotante = document.getElementById('mensajeFlotante');
                        mensajeFlotante.textContent = mensaje;
                        mensajeFlotante.classList.remove('oculto');
                        setTimeout(function() {
                            mensajeFlotante.classList.add('oculto');
                        }, 3000);
                    }
                </script>

</section>    
<section id="carrito">
    <h2>Carritos de Compras</h2>
    <button id="carritoBtn" onclick="abrirCarrito()">
        <i class="fas fa-shopping-cart"></i>
        <span id="cantidadCarrito">0</span>
    </button>
    <div id="carritoModal" class="modal">
        <div class="modal-content">
            <span class="cerrar" onclick="cerrarCarrito()">&times;</span>
            <h3>Productos en el Carrito</h3>
            <ul id="lista-carrito-modal"></ul>
            <button onclick="vaciarCarrito()">Vaciar Carrito</button>
            <button onclick="location.href='../usuario/pagos.php'">Pagar</button>
        </div>
    </div>
</section>
<script>
    function agregarAlCarrito(nombre, precio, imagen_path) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../usuario/agregar_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status === 200) {
                alert('Producto agregado al carrito');
                actualizarCarrito();
            } else {
                alert('Error al agregar producto al carrito');
            }
        };
        xhr.send(`nombre=${nombre}&precio=${precio}&imagen_path=${imagen_path}`);
    }

    function actualizarCarrito() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '../usuario/obtener_carrito.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                const productos = JSON.parse(this.responseText);
                const listaCarrito = document.getElementById('lista-carrito-modal');
                listaCarrito.innerHTML = '';
                productos.forEach(producto => {
                    const li = document.createElement('li');
                    li.innerHTML = `<img src="../administrador/uploads/${producto.imagen_path}" alt="${producto.nombre_producto}" width="50" height="50">
                                    <span>${producto.nombre_producto} - $${producto.precio} - Cantidad: ${producto.cantidad}</span>`;
                    listaCarrito.appendChild(li);
                });
                document.getElementById('cantidadCarrito').innerText = productos.length;
            } else {
                alert('Error al obtener productos del carrito');
            }
        };
        xhr.send();
    }

    function abrirCarrito() {
        document.getElementById('carritoModal').style.display = 'block';
        actualizarCarrito();
    }

    function cerrarCarrito() {
        document.getElementById('carritoModal').style.display = 'none';
    }

    function vaciarCarrito() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../usuario/vaciar_carrito.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                alert('Carrito vaciado');
                actualizarCarrito();
            } else {
                alert('Error al vaciar el carrito');
            }
        };
        xhr.send();
    }
</script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/646c794df3.js"></script>
    <script src="../includes/productos_script.js"></script>
    <script src="../includes/scriptPrincipal.js"></script>
    <script src="../includes/JSMenuUsuario.js"></script>
    <script src="../includes/ParaSubmenu.js"></script>
    <script src="../includes/JSFooter.js"></script>
    <hr size="2px" color="grey"/>
 
</body>
</html>
