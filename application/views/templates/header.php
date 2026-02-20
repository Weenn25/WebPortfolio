<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= isset($site_title) ? $site_title : 'Portfolio' ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="wrap"> 
        <h1 class="brand">
            <a href="<?= site_url() ?>" aria-label="<?= isset($site_title) ? $site_title : 'My Portfolio' ?>">
                <!-- Inline SVG logo: enlarged badge with stronger gradient, stroke, highlight and stylized R -->
                <span class="logo" aria-hidden="true">
                    <svg width="56" height="56" viewBox="0 0 56 56" role="img" xmlns="http://www.w3.org/2000/svg" focusable="false">
                        <defs>
                            <linearGradient id="logoGrad" x1="0" x2="1" y1="0" y2="1">
                                <stop offset="0" stop-color="rgb(10, 32, 228)" />
                                <stop offset="1" stop-color="#024" />
                            </linearGradient>
                            <filter id="logoShadow" x="-50%" y="-50%" width="200%" height="200%">
                                <feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#012" flood-opacity="0.25"/>
                            </filter>
                        </defs>
                        <rect width="56" height="56" rx="10" ry="10" fill="url(#logoGrad)" filter="url(#logoShadow)" stroke="#ffffff" stroke-opacity="0.06" stroke-width="1"/>
                        <!-- subtle glossy highlight -->
                        <rect x="6" y="6" width="44" height="22" rx="8" fill="rgba(255,255,255,0.06)" />
                        <text x="50%" y="62%" text-anchor="middle" fill="#ffffff" stroke="#012" stroke-opacity="0.18" stroke-width="0.9" paint-order="stroke" font-family="Segoe UI, Roboto, Arial, sans-serif" font-weight="900" font-size="26">R</text>
                    </svg>
                </span>
                <span class="sr-only"><?= isset($site_title) ? $site_title : 'My Portfolio' ?></span>
            </a>        
        </h1>
        
        <!-- Hamburger Toggle Button -->
        <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-controls="mainNav" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Main Navigation -->
        <nav class="main-nav" id="mainNav">
            <a href="<?= site_url('portfolio') ?>" class="<?= isset($page) && $page === 'home' ? 'active' : '' ?>">Home</a>
            <a href="<?= site_url('portfolio/about') ?>" class="<?= isset($page) && $page === 'about' ? 'active' : '' ?>">About</a>
            <a href="<?= site_url('portfolio/skills') ?>" class="<?= isset($page) && $page === 'skills' ? 'active' : '' ?>">Skills</a>
            <a href="<?= site_url('portfolio/projects') ?>" class="<?= isset($page) && $page === 'projects' ? 'active' : '' ?>">Projects</a>
            <a href="<?= site_url('portfolio/contact') ?>" class="<?= isset($page) && $page === 'contact' ? 'active' : '' ?>">Contact</a>
        </nav>
    </div>
</header>
<main class="wrap main_wrap">
    <!-- Content goes here -->

