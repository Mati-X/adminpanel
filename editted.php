<?php
require_once 'config.php';

session_start();

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$editted = json_decode($_COOKIE['editted'], true);
$page = (int)$_GET['page'] ?? 1;
if($page < 1) header('Location: editted.php?page=1');
$min = ($page-1)*10;
$products = [];
foreach($editted as $product) {
    $product = explode('-', $product);
    $pid = $product[0];
    $sid = $product[1];
    $lid = $product[2];

    $query1 = $db->prepare('SELECT distinct `Img`.`id_image`,`Prod`.`reference`, `PLang`.`link_rewrite`, `PLang`.`id_product`, `PLang`.`id_shop`, `PLang`.`id_lang`, `PLang`.`name`, `PLang`.`description`, `PLang`.`meta_keywords`, `PLang`.`meta_description`, `PLang`.`meta_title`, `Supp`.`name` AS supplier_name 
                       FROM gtrfvdcserdg_image AS Img,gtrfvdcserdg_product AS Prod ,gtrfvdcserdg_product_lang AS PLang, gtrfvdcserdg_supplier AS Supp 
                       WHERE PLang.id_product = '.$pid.' AND PLang.id_shop = '.$sid.' AND Img.id_product =  PLang.id_product AND Prod.id_product = PLang.id_product AND Prod.id_supplier = Supp.id_supplier AND PLang.id_lang = 1');
    $query1->execute();
    $products[$pid] = $query1->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <table class="table table-striped caption-top">
        <a href="index.php">Wróć do Wszystkich</a>
        <caption>Lista produktów zedytowanych</caption>
        <?php 
        if(empty($products)) {
            echo '<tr>';
            echo '<th>Brak produktów do edycji</th>';
            echo '<th><a href="index.php?page=0">Pierwsza Strona</a></th>';
            echo '<th><a href="index.php?page='.$total.'">Ostatnia Strona</a></th>';
            echo '</tr>';
            exit;
        }
        ?>
        <tr>
            <th>*</th>
            <th>ID Produktu</th>
            <th>Zdjecie</th>
            <th>Nazwa Produktu</th> 
            <th>Opis Produktu</th>
            <th>Meta Keywords</th>
            <th>Meta Description</th>
            <th>Meta Title</th>
        </tr>
    <?php
    foreach ($products as $product) {
        $image = 'https://intimashop.eu/'.$product['id_image'].'-large_default/'.$product['link_rewrite'].'.jpg';
        echo '<tr>';
        echo '<td><a href="edycja.php?product='.$product['id_product'].'&shop='.$product['id_shop'].'&lang='.$product['id_lang'].'">Edytuj</a></td>';
        echo '<td>'.$product['id_product'].'</td>';
        echo '<td><img style="max-height:100px" alt='. $product['link_rewrite'] .' class="img-thumbnail" src="'.$image.'"></td>';
        echo '<td>' . $product['name'] . '</td>';
        echo '<td class="text-truncate" style="max-width: 150px;">' . $product['description'] . '</td>';
        echo '<td class="text-truncate" style="max-width: 150px;">' . $product['meta_keywords'] . '</td>';
        echo '<td class="text-truncate" style="max-width: 150px; ">' . $product['meta_description'] . '</td>';
        echo '<td class="text-truncate" style="max-width: 150px;">' . $product['meta_title'] . '</td>';
        echo '</tr>';
    }
    ?>
    </table>
</body>
</html>