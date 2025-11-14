<?php
$ins_item->execute();
// disminuir stock
$upd = $mysqli->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
$upd->bind_param('ii', $qty, $pid); $upd->execute();
}
// limpiar carrito
unset($_SESSION['cart']);
$success = 'Pedido realizado. ID: '.$order_id;
}
}
}


$cart = $_SESSION['cart'] ?? [];
$products = [];
if(!empty($cart)){
$ids = array_map('intval', array_keys($cart));
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$res = $stmt->get_result();
while($r = $res->fetch_assoc()) $products[$r['id']] = $r;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Carrito</title><link rel="stylesheet" href="00_styles.css"></head>
<body>
<div class="container">
<div class="header"><h2>Carrito</h2><div><a href="products.php">Seguir comprando</a> | <?php if(is_logged_in()): ?><a href="logout.php">Salir</a><?php else:?><a href="login.php">Login</a><?php endif; ?></div></div>
<?php if(!empty($error)) echo '<p style="color:red">'.$error.'</p>'; ?>
<?php if(!empty($success)) echo '<p style="color:green">'.$success.'</p>'; ?>
<?php if(empty($cart)): ?>
<p>Tu carrito está vacío.</p>
<?php else: ?>
<table>
<tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th>Acción</th></tr>
<?php $sum=0; foreach($cart as $pid => $qty): $p = $products[$pid]; $sub = $p['price']*$qty; $sum += $sub; ?>
<tr>
<td><?=htmlspecialchars($p['name'])?></td>
<td>$<?=number_format($p['price'],2,',','.')?></td>
<td><?=$qty?></td>
<td>$<?=number_format($sub,2,',','.')?></td>
<td><a href="cart.php?remove=<?=$pid?>">Eliminar</a></td>
</tr>
<?php endforeach; ?>
</table>
<p>Total: $<?=number_format($sum,2,',','.')?></p>
<form method="post"><button class="button" name="checkout">Finalizar compra</button></form>
<?php endif; ?>
</div>
</body></html>