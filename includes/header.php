<?php
/**
 * Header Template
 * 
 * Common HTML head section for all pages.
 * Include this at the top of every page.
 * 
 * Usage: 
 *   $pageTitle = "Dashboard";
 *   require_once __DIR__ . '/../includes/header.php';
 */

// Ensure $pageTitle is set
if (!isset($pageTitle)) {
    $pageTitle = 'Library Management System';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Library Management System - Manage books, users, and borrowings">
    <title><?= htmlspecialchars($pageTitle) ?> - Library System</title>
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/navigation.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/dropdown.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/library.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/manage-shelf.css') ?>">
</head>
<body>

