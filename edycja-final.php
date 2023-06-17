<?php 
require_once 'config.php';
session_start();

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

 $name = $_POST['name'] ?? NULL;
 $description = $_POST['description'] ?? NULL;
 $meta_keywords = $_POST['meta_keywords'] ?? NULL;
 $meta_description = $_POST['meta_description'] ?? NULL;
 $meta_title = $_POST['meta_title'] ?? NULL;
 $pid = $_POST['product_id'] ?? NULL;
 $sid = $_POST['shop_id'] ?? NULL;
 $lid = $_POST['lang_id'] ?? NULL;


 $query = $db->prepare('UPDATE gfwewrgfdvd_product_lang SET name = :name, description = :description, meta_keywords = :meta_keywords, meta_description = :meta_description, meta_title = :meta_title WHERE id_product = :id_product AND id_shop = :id_shop AND id_lang = :id_lang');
 $query->execute([
    ':name' => $name,
    ':description' => $description,
    ':meta_keywords' => $meta_keywords,
    ':meta_description' => $meta_description,
    ':meta_title' => $meta_title,
    ':id_product' => $pid,
    ':id_shop' => $sid,
    ':id_lang' => $lid
]);
 $query->execute();

 $queryget = $db->prepare('SELECT id_category_default FROM gfwewrgfdvd_product WHERE id_product = :id_product');
    $queryget->execute([
        ':id_product' => $pid,
    ]);
    $category = $queryget->fetch(PDO::FETCH_ASSOC);
    $category = $category['id_category_default'];

 $editted = json_decode($_COOKIE['editted']);
 $editted[] = $pid.'-'.$sid.'-'.$lid;
 setcookie('editted', json_encode($editted), time() + (10 * 365 * 24 * 60 * 60));


    $query = $db->prepare("SELECT PLang.id_product FROM gfwewrgfdvd_product_lang AS PLang,gfwewrgfdvd_product AS Lang WHERE Lang.id_product = PLang.id_product AND Lang.id_category_default = $category AND PLang.id_product > $pid AND PLang.id_shop = 1 AND PLang.id_lang = 1 LIMIT 1");
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $next = $row['id_product'];
 header('Location: edycja.php?product='.$next.'&shop=1&lang=1');
?>