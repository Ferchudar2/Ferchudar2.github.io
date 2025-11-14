<?php
require 'db.php';
if(!is_logged_in() || !is_admin()){ header('Location: login.php'); exit; }
$res = $mysqli->query('SELECT o.*, u.username FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC');
?>
<!doctype html><html><head><meta charset="utf-8"><title>Pedidos</title><link rel="stylesheet" href="00_styles.css"></head>
<body>
<div class="container">
<div class="header"><h2>Pedidos</h2><div><a href="admin_products.php">Volver</a> | <a href="logout.php">Salir</a></div></div>
<?php while($o = $res->fetch_assoc()): ?>
<div style="border:1px solid #ddd;padding:8px;margin:8px;">
<strong>Pedido ID <?=$o['id']?></strong> - Usuario: <?=$o['username']?> - Total: $<?=number_format($o['total'],2,',','.')?> - Fecha: <?=$o['created_at']?>
<div>
<?php
$it = $mysqli->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?');
$it->bind_param('i',$o['id']); $it->execute(); $rit = $it->get_result();
while($row = $rit->fetch_assoc()){
echo '<div>'.$row['qty'].' x '.htmlspecialchars($row['name']).' ($'.number_format($row['price'],2,',','.').')</div>';
}
?>
</div>
</div>
<?php endwhile; ?>
</div>
</body></html>