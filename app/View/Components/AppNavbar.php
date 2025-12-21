<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppNavbar extends Component
{
    public array $links;
    public string $homeRoute;
    public string $subtitle;

    public function __construct()
    {
        // kalau URL /admin/* atau route name admin.* => admin context
        $isAdmin = request()->is('admin/*') || request()->routeIs('admin.*');

        $base = $isAdmin ? 'admin' : 'app';

        $this->homeRoute = $base . '.dashboard';
        $this->subtitle  = $isAdmin ? 'Admin Dashboard' : 'User Dashboard';

        $this->links = [
            ['label' => 'Dashboard', 'route' => $base . '.dashboard'],
            ['label' => 'Form',      'route' => $base . '.forms.index'],

            // ini yang lu butuhin biar muncul
            ['label' => 'Order',     'route' => $base . '.orders.index'],
            ['label' => 'Abandoned', 'route' => $base . '.abandoned.index'],

            // Profil (ini link route-nya memang 'profile.edit' dari web.php lu)
            ['label' => 'Profil',    'route' => 'profile.edit'],
        ];

        // Settings cuma ada di app (di route app.php lu ada settings)
        if (!$isAdmin) {
            $this->links[] = ['label' => 'Settings', 'route' => 'app.settings.index'];
        }
    }

    public function render(): View
    {
        return view('components.app-navbar');
    }
}
