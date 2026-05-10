<?php
/**
 * logout.php — Déconnexion de l'utilisateur
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

logout();
setFlash('info', 'Vous avez été déconnecté avec succès.');
redirect('login.php');
