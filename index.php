<?php
//Iniciamos una sesión, para poder guardar lo que vamos a ir agregando y quitando de nuestro carrito
session_start();

//Conectamos con nuestra base de datos.
require_once('config.php');
$con = OpenCon();

//Acá empezamos a manejar la lógica de nuestro carrito. Como pueden ver, vamos a chequear que nos esté llegando una "acción". Si nos llega (lo verificamos con !empty($_GET["accion])), entonces pasamos al
//siguiente paso.
if(!empty($_GET["accion"])) {

//Abrimos un switch, con un $_GET que esta recibiendo la acción de la que hablabamos antes. Este Switch va a tener 3 casos: "Meter", "Quitar" y "Vaciar.
    switch($_GET["accion"]) {

//El primer caso es "Meter". Para esto, revisamos que la cantidad que recibamos con $_POST en nuestro carrito no sea 0.
//Si no lo esta, entonces pasamos el primer "if", y recibimos el código de nuestro producto (una variable para identificarlo), y hacemos la query para traer el producto que coincida con ese código.
//Luego, creamos un array con nuestros productos.
        case "meter":
            if(!empty($_POST["cantidad"])) {
                $codigoProducto = runQuery($con, "SELECT * FROM fruta WHERE codigo='" . $_GET["codigo"] . "'");
                $itemArray = array($codigoProducto[0]["codigo"]=>array('nombre'=>$codigoProducto[0]["nombre"], 'codigo'=>$codigoProducto[0]["codigo"], 'cantidad'=>$_POST["cantidad"], 'precio'=>$codigoProducto[0]["precio"], 'imagen'=>$codigoProducto[0]["imagen"]));

//Ahora, vamos a otros dos "if". El primero chequea que el carrito de nuestra sesión no esté vacio. Si no lo está, pasamos al segundo "if", en donde revisamos si los productos en el array comparten
//el mismo código. En caso de que si, entonces sabemos que son el mismo producto, y el carrito entonces sabrá la cantidad que tiene de ese producto en específico.
                if(!empty($_SESSION["item_carrito"])) {
                    if(in_array($codigoProducto[0]["codigo"],array_keys($_SESSION["item_carrito"]))) {
                        foreach($_SESSION["item_carrito"] as $k => $v) {
                                if($codigoProducto[0]["codigo"] == $k) {
                                    if(empty($_SESSION["item_carrito"][$k]["cantidad"])) {
                                        $_SESSION["item_carrito"][$k]["cantidad"] = 0;
                                    }
                                    $_SESSION["item_carrito"][$k]["cantidad"] += $_POST["cantidad"];
                                }
                        }
//Si ya tenemos un carrito armado, pero agregamos más cosas a este luego, vamos a unir el array que armamos ahora con el que ya está en el carrito de nuestra sesión.
                    } else {
                        $_SESSION["item_carrito"] = array_merge($_SESSION["item_carrito"],$itemArray);
                    }
//Si no tenemos carrito en esta sesión, creamos uno nuevo con el array que recibimos.
                } else {
                    $_SESSION["item_carrito"] = $itemArray;
                }
            }
        break;

//En el caso "quitar", revisamos que el carrito no esté vacío. Si no lo esta, revisa qué código que recibió de un producto (Lo recibe con un botón que veremos más abajo) y quita al producto con ese
//código del carrito.
        case "quitar":
            if(!empty($_SESSION["item_carrito"])) {
                foreach($_SESSION["item_carrito"] as $k => $v) {
                        if($_GET["codigo"] == $k)
                            unset($_SESSION["item_carrito"][$k]);	
//Si el carrito quedaría vacío, quitamos el carrito directamente. 			
                        if(empty($_SESSION["item_carrito"]))
                            unset($_SESSION["item_carrito"]);
                }
            }
        break;

//El ultimo caso es Vaciar. Quita el carrito enteramente.
        case "vaciar":
            unset($_SESSION["item_carrito"]);
        break;	
    }
    }
    ?>

<!--Ahora pasamos al HTML. Como siempre, ponemos nuestra hoja de estilos y lo que necesitemos en el head -->
<html>
<head>
    <title>Carrito</title>
    <link href="estilo.css" type="text/css" rel="stylesheet">
</head>

<!--Acá empieza el body, en donde veremos a nuestro carrito-->
<body>
<!--Nuestro carrito. Tiene un botón de vaciar, que hace referencia a la acción vaciar, que armamos arriba. Al clickearlo, entonces ejecutará esa acción-->
<div id="shopping-cart">
    <div><h1>Carrito</h1></div>
    <a id="botonVaciar" href="index.php?accion=vaciar">Vaciar Carrito</a>

<!--Ahora vamos a chequear que el carrito exista. Si existe, por defecto va a poner todos los valores en 0.
Luego, haremos una tabla en donde se mostrarán los productos que hay actualmente en el carro. -->
    <?php
if(isset($_SESSION["item_carrito"])){
    $cantidad_total = 0;
    $precio_total = 0;
?>	
<table class="tabla-carrito" cellpadding="10" cellspacing="1">
<tbody>
<tr>
<!--los titulos de cada columna de nuestra tabla-->
<th style="text-align:left;">Nombre</th>
<th style="text-align:left;">Código</th>
<th style="text-align:right;" width="5%">Cantidad</th>
<th style="text-align:right;" width="10%">Precio</th>
<th style="text-align:right;" width="10%">Precio total</th>
<th style="text-align:center;" width="5%">Quitar</th>
</tr>	
<!--Acá armamos un foreach con todos los items del carrito. Revisa los items del array de items que vimos arriba, y por cada uno hará una fila dentro de nuestro carrito, mostrando entonces
los contenidos de este. Recibe los nombres, cantidades y precios del array para mostrarlos con un echo
 También, cada producto que metamos en el carrito incluye un botón para quitar ese producto del carrito.
Como pueden ver, hace href a la acción "quitar" que armamos arriba, y le pasa también el código del producto-->
<?php		
    foreach ($_SESSION["item_carrito"] as $item){
        $item_precio = $item["cantidad"]*$item["precio"];
		?>
				<tr>
				<td><img width="150" height="150" src="<?php echo $item["imagen"]; ?>" class="imagen-item-carrito" /><?php echo $item["nombre"]; ?></td>
				<td><?php echo $item["codigo"]; ?></td>
				<td style="text-align:right;"><?php echo $item["cantidad"]; ?></td>
				<td  style="text-align:right;"><?php echo "$ ".$item["precio"]; ?></td>
				<td  style="text-align:right;"><?php echo "$ ". number_format($item_precio,2); ?></td>
				<td style="text-align:center;"><a href="index.php?accion=quitar&codigo=<?php echo $item["codigo"]; ?>" class="botonQuitarAccion"><img src="icon-delete.png" alt="Quitar item" /></a></td>
				</tr>
				<?php
                //$cantidad_total suma las cantidades entre los distintos productos. Si tenemos 2 manzanas y 1 naranja, entonces haría 2+1
				$cantidad_total += $item["cantidad"];
                //$precio_total hace lo mismo, pero sumando sus precios.
				$precio_total += ($item["precio"]*$item["cantidad"]);
		}
		?>
<!--Finalmente, en una última fila, mostramos el precio y la cantidad total de lo que hay en nuestro carrito-->
<tr>
<td colspan="2" align="right">Total:</td>
<td align="right"><?php echo $cantidad_total; ?></td>
<td align="right" colspan="2"><strong><?php echo "$ ".number_format($precio_total, 2); ?></strong></td>
<td></td>
</tr>
</tbody>
</table>	
<!--Para terminar, si no hay nada en el carrito, simplemente mostraremos un cartel que dice "El carrito esta vacío"-->	
  <?php
}else {
?>
    <div class="no-hay-nada">El carrito esta vacío</div>
<?php 
    }
?>
</div>

<!--Y finalmente, necesitaremos una grilla con los productos que podemos agregar a nuestro carrito. Los productos los recibimos de nuestra base de datos
 Primero, creamos un array vacío en donde vamos a guardar nuestros productos.
 Luego, en otra variable que llamaremos $resultado, haremos la query para obtener los productos dentro del metodo mysqli_query().
 Después, vamos a armar un while, que iguala una variable $fila al resultado de la query, dentro del metodo mysqli_fetch_array().
 mysqli_fetch_array() guardará los resultados como entradas en un array. El while se acabarà cuando ya no haya más para agregar a ese array (es decir, que ya vio todos los productos.).
 Después, le pasamos lo que recibimos en $fila a nuestro array de productos, y salimos del While.
Luego, se revisa que nuestro array no este vacío. Si no lo está, hará un foreach, que creará un cuadro por cada entrada en nuestra base de datos.
Como pueden ver, esto lo manejamos con un form, con un method="post" cuyas acción es "meter", y de paso también pasa el código del producto.
Finalmente, hay un botón con un "submit" que toma la cantidad del producto, y la lleva a la acción "meter", para que haga su trabajo tal y como vimos antes.-->
<div id="grilla-productos">
	<div class="txt-heading">Productos</div>
	<?php
    $array_productos = [];
	$resultado = mysqli_query($con, "SELECT * FROM fruta ORDER BY id_fruta ASC");
    while($fila = mysqli_fetch_array($resultado))
    {
        $array_productos[] = $fila;
    }

	if (!empty($array_productos)) { 
		foreach($array_productos as $clave=>$valor){
	?>
		<div class="item-producto">
			<form method="post" action="index.php?accion=meter&codigo=<?php echo $array_productos[$clave]["codigo"]; ?>">
			<div class="product-image"><img width="150" height="150" src="<?php echo $array_productos[$clave]["imagen"]; ?>"></div>
			<div class="product-tile-footer">
			<div class="product-title"><?php echo $array_productos[$clave]["nombre"]; ?></div>
			<div class="product-price"><?php echo "$".$array_productos[$clave]["precio"]; ?></div>
			<div class="cart-action"><input type="text" class="cantidad-producto" name="cantidad" value="1" size="2" /><input type="submit" value="Agregar al Carrito" class="botonAgregarAccion" /></div>
			</div>
			</form>
		</div>
	<?php
		}
	}
	?>
</div>

</body>
</html>