<?php
require __DIR__.'/includes/functions.php';
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);echo json_encode(['error'=>'POST required']);exit;}
if(!user()){http_response_code(401);echo json_encode(['error'=>'Please sign in to save articles.','login'=>url('login.php')]);exit;}
check_csrf();$u=user();$id=(int)($_POST['id']??0);
$s=db()->prepare("SELECT id FROM articles WHERE id=? AND status='approved'");$s->execute([$id]);if(!$s->fetchColumn()){http_response_code(404);echo json_encode(['error'=>'Article not found']);exit;}
$isSaved=saved($id);$s=db()->prepare($isSaved?'DELETE FROM bookmarks WHERE user_id=? AND article_id=?':'INSERT INTO bookmarks(user_id,article_id) VALUES(?,?)');$s->execute([$u['id'],$id]);echo json_encode(['saved'=>!$isSaved,'message'=>$isSaved?'Removed from saved articles':'Article saved to your reading list']);
