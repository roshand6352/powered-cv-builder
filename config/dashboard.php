<?php

return [

    'navigation' => [
        [
            'label' => 'Overview',
            'route' => 'dashboard',
            'icon' => 'home',
            'active' => 'dashboard',
        ],
        [
            'label' => 'CVs',
            'route' => 'dashboard.cvs',
            'icon' => 'document',
            'active' => 'dashboard.cvs',
        ],
        [
            'label' => 'Cover Letters',
            'route' => 'dashboard.cover-letters',
            'icon' => 'mail',
            'active' => 'dashboard.cover-letters',
        ],
        [
            'label' => 'Subscription',
            'route' => 'dashboard.subscription',
            'icon' => 'credit-card',
            'active' => 'dashboard.subscription',
        ],
        [
            'label' => 'Billing',
            'route' => 'dashboard.billing',
            'icon' => 'receipt',
            'active' => 'dashboard.billing',
        ],
        [
            'label' => 'Profile',
            'route' => 'dashboard.profile',
            'icon' => 'user',
            'active' => 'dashboard.profile',
        ],
    ],

];
