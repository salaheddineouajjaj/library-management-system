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
    <!-- Google Fonts for Library Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <!-- Library Theme - MUST come first for dramatic visual change -->
    <link rel="stylesheet" href="<?= url('/css/library-theme.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/navigation.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/auth-library.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/dropdown.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/library.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/manage-shelf.css') ?>">
</head>
<body>

