<?php
session_start();

$host = 'localhost';
$dbname = 'devoir';
$username = 'root'; 
$password = '';     

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}

function isLoggedIn()
{
  return isset($_SESSION['user_id']);
}


function isAdmin(){
  return isLoggedIn() && $_SESSION['role'] === 'admin';
}
