<?php
require_once 'config.php';

session_start();

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$product_id = (int)$_GET['product'] ?? 0;
$shop_id = (int)$_GET['shop'] ?? 0;
$lang_id = (int)$_GET['lang'] ?? 0;
$query = $db->prepare('SELECT  `Img`.`id_image`, `Prod`.`reference`, `Manu`.`name` AS MName, `PLang`.`link_rewrite`,`PLang`.`id_product`, `PLang`.`id_shop`, `PLang`.`id_lang`, `PLang`.`name`, `PLang`.`description`, `PLang`.`meta_keywords`, `PLang`.`meta_description`, `PLang`.`meta_title`, `Supp`.`name` AS supplier_name 
                       FROM `gfwewrgfdvd_manufacturer` AS Manu, gfwewrgfdvd_image AS Img, gfwewrgfdvd_product AS Prod ,gfwewrgfdvd_product_lang AS PLang, gfwewrgfdvd_supplier AS Supp 
                       WHERE Manu.id_manufacturer = Prod.id_manufacturer AND Img.id_product =  PLang.id_product AND PLang.id_product = :product_id AND PLang.id_shop = :shop_id AND PLang.id_lang = :lang_id AND Prod.id_product = PLang.id_product AND Prod.id_supplier = Supp.id_supplier'
);
$query->execute([
    'product_id' => $product_id,
    'shop_id' => $shop_id,
    'lang_id' => $lang_id
]);
$product = $query->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/r8esqevwsyb2ee9ixjsnej8f6e2xl7d1ut1044v4eionqjdr/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
<table class="table table-striped">
        <tr>
        <?php 
        if(empty($product)) {
            echo '<th style="text-align: center" colspan=4>Brak produktów do edycji</th>';
            echo '<th style="text-align: center" colspan=4><a href="index.php">Pierwsza Strona</a></th>';
            exit;
        }
        ?>
        </tr>
        <form method="POST" action="edycja-final.php">
    <?php
        $image = 'https://abuabu.pl/'.$product['id_image'].'-large_default/'.$product['link_rewrite'].'.jpg';
        echo '<tr>';
        echo '<th>Nazwa Produktu</th>';
        echo '<td><textarea name="name" oninput="ChangeName()"  class="form-control" >' . $product['name'] . '</textarea></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Zdjecie</th>';
        echo '<td><img style="max-height:300px" alt='. $product['link_rewrite'] .' class="img-thumbnail" src="'.$image.'"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Opis Produktu</th>';
        echo '<td><textarea name="description" maxlength="130" oninput="ChangeMetaDesc()" class="editor" style="min-height:250px" >' . $product['description'] . '</textarea></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Meta Keywords</th>';
        echo '<td><textarea name="meta_keywords" maxlength="130" class="form-control" >'.$product['name'].', '.$product['MName'].', '.$product['reference'].'</textarea></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Meta Description</th>';
        echo '<td><textarea name="meta_description" maxlength="130" class="form-control" style="min-height:250px" >' . $product['description'] . '</textarea></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Meta Title</th>';
        echo '<td><textarea name="meta_title" maxlength="130" class="form-control" >'.$product['name'].' | '.$product['supplier_name'].' | '.$product['reference'].'</textarea></td>';
        echo '<input type="hidden" name="product_id" value="'.$product['id_product'].'">';
        echo '<input type="hidden" name="shop_id" value="'.$product['id_shop'].'">';
        echo '<input type="hidden" name="lang_id" value="'.$product['id_lang'].'">';
        echo '</tr>';
    ?>
    <tr><td><input type='submit' value='Zapisz'></td><td><a href='index.php'>Anuluj</a></td></tr>
</form>
    </table>
    <script>


      function myCustomOnInit() {
            let meta_desc = document.getElementsByName('meta_description')[0];
            let div = document.createElement("div");
            div.innerHTML = meta_desc.value;
            meta_desc.value = div.innerText;
	  let name = document.getElementsByName('name')[0];
	  let newName = name.value.replace(/([^a-zA-Z0-9])\1+/g, '$1');
	  name.value = newName;
	  ChangeName();
        }
        
        function ChangeMetaDesc(desc) {
            let meta_desc = document.getElementsByName('meta_description')[0];
            let div = document.createElement("div");
            div.innerHTML = desc.currentTarget.innerHTML;
            meta_desc.value = div.innerText;
        }
        function ChangeName() {
            let name = document.getElementsByName('name')[0];
            let meta_keywords = document.getElementsByName('meta_keywords')[0];
            let meta_title = document.getElementsByName('meta_title')[0];
            meta_keywords.value = name.value + ', ' + '<?php echo $product['MName']; ?>' + ', ' + '<?php echo $product['reference']; ?>';
            meta_title.value = name.value + ' | ' + '<?php echo $product['supplier_name']; ?>' + ' | ' + '<?php echo $product['reference']; ?>';
        }
    tinymce.init({ selector:'textarea.editor' ,
	language : 'pl', 
	plugins: 'wordcount',
	toolbar: 'wordcount',
        setup: function(editor) {
    editor.on('input', function(e) {
        ChangeMetaDesc(e);
    });
    editor.on('init', function(e) {
        myCustomOnInit();
    });
  },
    });
    </script>
</body>
</html>