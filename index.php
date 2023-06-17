<?php
require_once 'config.php';

session_start();

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}


$page = (int)$_GET['page'] ?? 1;
$category = (int)$_GET['category'] ?? NULL; 
$certainid = $_GET['certainid'] ?? NULL;
if(isset($certainid)) {
  header('Location: edycja.php?product='.$certainid.'&shop=1&lang=1');
}
if($page < 1) header('Location: index.php?page=1&category='.$category);
$min = ($page-1)*10;

if($category == NULL) {
$query1 = $db->prepare('SELECT distinct `Img`.`id_image`,`Prod`.`reference`, `PLang`.`link_rewrite`, `PLang`.`id_product`, `PLang`.`id_shop`, `PLang`.`id_lang`, `PLang`.`name`, `PLang`.`description`, `PLang`.`meta_keywords`, `PLang`.`meta_description`, `PLang`.`meta_title`, `Supp`.`name` AS supplier_name 
                       FROM gfwewrgfdvd_image AS Img,gfwewrgfdvd_product AS Prod ,gfwewrgfdvd_product_lang AS PLang, gfwewrgfdvd_supplier AS Supp 
                       WHERE Img.id_product =  PLang.id_product AND Prod.id_product = PLang.id_product AND Prod.id_supplier = Supp.id_supplier AND PLang.id_shop = 1 AND PLang.id_lang = 1 LIMIT 10 OFFSET '.$min);
$query1->execute();
}
else
{
  $query1 = $db->prepare('SELECT distinct `Img`.`id_image`,`Prod`.`reference`, `PLang`.`link_rewrite`, `PLang`.`id_product`, `PLang`.`id_shop`, `PLang`.`id_lang`, `PLang`.`name`, `PLang`.`description`, `PLang`.`meta_keywords`, `PLang`.`meta_description`, `PLang`.`meta_title`, `Supp`.`name` AS supplier_name 
                       FROM gfwewrgfdvd_image AS Img, `gfwewrgfdvd_category_product` AS CProd,gfwewrgfdvd_product AS Prod ,gfwewrgfdvd_product_lang AS PLang, gfwewrgfdvd_supplier AS Supp 
                       WHERE Prod.id_category_default = :category AND  Img.id_product =  PLang.id_product AND Prod.id_product = PLang.id_product AND Prod.id_supplier = Supp.id_supplier AND PLang.id_shop = 1 AND PLang.id_lang = 1 LIMIT 10 OFFSET '.$min);
$query1->execute([
  'category' => $category
]);
$a = [];
$querypath = $db->prepare('SELECT `id_category`,`id_parent` FROM gfwewrgfdvd_category WHERE id_category = :category AND id_shop_default= 1');
$querypath->execute([
  'category' => $category
]);
$rowpath = $querypath->fetch(PDO::FETCH_ASSOC);
$parent = $rowpath['id_parent'];
$current = $rowpath['id_category'];
$a[] = $current;
$a[] = $parent;
while($parent != 2)
{
  $querypath = $db->prepare('SELECT `id_parent` FROM gfwewrgfdvd_category WHERE id_category = :category AND id_shop_default = 1');
  $querypath->execute([
    'category' => $parent
  ]);
  $rowpath = $querypath->fetch(PDO::FETCH_ASSOC);
  $parent = $rowpath['id_parent'];
  $a[] = $parent;
}

foreach ($a as $key => $value) {
  $querypath = $db->prepare('SELECT `name` FROM gfwewrgfdvd_category_lang WHERE id_category = :category AND id_shop = 1 AND id_lang = 1');
  $querypath->execute([
    'category' => $value
  ]);
  $rowpath = $querypath->fetch(PDO::FETCH_ASSOC);
  $b[$key] = $rowpath['name'];
}
$b = array_reverse($b);
}



$products = $query1->fetchAll(PDO::FETCH_ASSOC);

if($category == NULL) {
$query2 = $db->prepare('SELECT  COUNT(*) FROM gfwewrgfdvd_product AS Prod ,gfwewrgfdvd_product_lang AS PLang, gfwewrgfdvd_supplier AS Supp 
WHERE Prod.id_product = PLang.id_product AND Prod.id_supplier = Supp.id_supplier AND  PLang.id_lang = 1 AND PLang.id_shop = 1 ');

$query2->execute();

$query3 = $db->prepare('SELECT `CLang`.`name`, `Cat`.`id_parent`, `CLang`.`id_category` FROM `gfwewrgfdvd_category_lang` AS CLang, `gfwewrgfdvd_category` AS Cat
                      WHERE id_shop = 1 AND id_lang = 1 AND Cat.id_parent = 2 AND CLang.id_category = Cat.id_category');
$query3->execute();
$categories = $query3->fetchAll();

}
else
{
$query2 = $db->prepare('SELECT COUNT(*) FROM gfwewrgfdvd_product AS Prod ,gfwewrgfdvd_product_lang AS PLang, gfwewrgfdvd_supplier AS Supp 
WHERE Prod.id_category_default = :category AND Prod.id_product = PLang.id_product AND Prod.id_supplier = Supp.id_supplier AND PLang.id_lang = 1 AND PLang.id_shop = 1 ');
$query2->execute([
  'category' => $category
]);

$query3 = $db->prepare('SELECT `CLang`.`name`, `Cat`.`id_parent`, `CLang`.`id_category` FROM `gfwewrgfdvd_category_lang` AS CLang, `gfwewrgfdvd_category` AS Cat
                      WHERE id_shop = 1 AND id_lang = 1 AND Cat.id_parent = :cat AND CLang.id_category = Cat.id_category');
$query3->execute([
  'cat' => $category
]);
$categories = $query3->fetchAll();

}
$total = $query2->fetchColumn();
$total = ceil($total/10);



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
        <a href="editted.php">Zedytowane</a>
        <caption>Lista produktów do edycji Strona <?=$page?> z <?=$total?></caption>
        <?php if(isset($b)): ?>
        <tr><th colspan= 8 >
        <?php foreach($b as $key => $value): ?>
        <?=$value?>/
        <?php endforeach; ?></th>
        </tr>
        <?php endif; ?>
        <tr>
        <th colspan=8><a class="btn btn-secondary btn-sm m-1" href="index.php?page=1">Wszystkie</a>
          <?php foreach($categories as $cat): ?>
            <a class="btn btn-primary btn-sm m-1" href="index.php?category=<?=$cat['id_category']?>&page=1"><?=$cat['name']?></a>
          <?php endforeach; ?>
          </th>
        </tr>
        <form method='GET'>
        <tr>
            <th colspan="3"><input class="form-control" placeholder="Strona" name='page'></th>
            <input name="category" type="hidden" value="<?=$category?>" >
            <th><input class="form-control" type='submit'></th>
        </form>
        <th colspan="2"><a class="btn btn-primary" style="width:100%" href="index.php?page=<?=$page-1?>&category=<?=$category?>"><</a></th>
        <th colspan="2"><a class="btn btn-primary" style="width:100%" href="index.php?page=<?=$page+1?>&category=<?=$category?>">></a></th>
        </tr>
        <tr>
        <form method='GET'>
        <tr>
            <th colspan="3"><input class="form-control" placeholder="ID Produktu" name='certainid'></th>
            <input name="category" type="hidden" value="<?=$category?>" >
            <input name="page" type="hidden" value="1" >
            <th><input class="form-control" type='submit'></th>
        </form>
        </tr>
        <tr>
            <th>*</th>
            <th>ID Produktu</th>
            <th>Index Produktu</th>
            <th>Zdjecie</th>
            <th>Nazwa Produktu</th> 
            <th>Opis Produktu</th>
            <th>Meta Keywords</th>
            <th>Meta Description</th>
            <th>Meta Title</th>
        </tr>
    <?php
    if(empty($products)) {
        echo '<tr>';
        echo '<th style="text-align:center" colspan=4>Brak produktów do edycji</th>';
        echo '<th style="text-align:center"  colspan=4><a href="index.php?page=0">Pierwsza Strona</a></th>';
        echo '</tr>';
        exit;
    }
    foreach($products as $product){
      $newproducts[$product['id_product']] = $product;
    }
        
    foreach ($newproducts as $product) {

        $image = 'https://abuabu.pl/'.$product['id_image'].'-large_default/'.$product['link_rewrite'].'.jpg';
        echo '<tr>';
        echo '<td><a href="edycja.php?product='.$product['id_product'].'&shop='.$product['id_shop'].'&lang='.$product['id_lang'].'">Edytuj</a></td>';
        echo '<td>'.$product['id_product'].'</td>';
        echo '<td>'.$product['reference'].'</td>';
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