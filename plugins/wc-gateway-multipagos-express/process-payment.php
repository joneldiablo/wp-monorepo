<?php
$action = $_GET['action'];
$order_id = $_GET['order_id'];
$prefix = $_GET['prefix'];
$total = $_GET['total'];
$return = $_GET['return'];
$ref = $prefix . $order_id;
$idexpress = $_GET['idexpress'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Multipagos Express</title>
</head>

<body>
  Cargando...
  <form action="<?php echo $action ?>" method="post" id="middle">
    <input type="hidden" name="importe" value="<?php echo $total ?>" />
    <input type="hidden" name="referencia" value="<?php echo $ref ?>" />
    <input type="hidden" name="urlretorno" value="<?php echo $return ?>" />
    <input type="hidden" name="idexpress" value="<?php echo $idexpress ?>" />
    <input type="hidden" name="financiamiento" value="0" />
    <input type="hidden" name="plazos" value="" />
    <input type="hidden" name="mediospago" value="100000" />
  </form>
  <script>
    document.querySelector('form#middle').submit();
  </script>
</body>

</html>

<?php exit() ?>