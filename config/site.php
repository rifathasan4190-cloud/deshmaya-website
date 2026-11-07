<?php

// Public navigation. `enabled => false` items are hidden until the open decisions (D-10, D-11) are settled.
return [
    'menu' => [
        ['label' => 'Home', 'route' => 'home', 'enabled' => true],
        ['label' => 'About Us', 'route' => 'about', 'enabled' => true],
        ['label' => 'Products', 'route' => 'products.index', 'enabled' => true],
        ['label' => 'Export Markets', 'route' => 'export-markets', 'enabled' => true],
        ['label' => 'Certification', 'route' => 'certification', 'enabled' => true],
        ['label' => 'Quality & Packaging', 'route' => 'quality-packaging', 'enabled' => false],
        ['label' => 'How We Export', 'route' => 'how-we-export', 'enabled' => true],
        ['label' => 'Contact', 'route' => 'contact', 'enabled' => true],
        ['label' => 'Blog', 'route' => null, 'enabled' => false],
    ],
];
