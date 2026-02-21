<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * MenuRegistry
 *
 * Collects and manages sidebar menu items registered by modules.
 * Each module can register its menu via a menu.php config file.
 */
class MenuRegistry
{
    /**
     * Registered menu sections.
     *
     * @var array
     */
    protected array $menus = [];

    /**
     * Register a menu section from a module.
     *
     * @param array $config Menu configuration array with keys:
     *   - header: string (section header label)
     *   - roles: array (allowed roles)
     *   - order: int (display order, lower = higher)
     *   - items: array (menu items)
     */
    public function register(array $config): void
    {
        $header = $config['header'] ?? 'LAINNYA';
        $roles  = $config['roles'] ?? [];
        $order  = $config['order'] ?? 100;
        $items  = $config['items'] ?? [];

        // If a header already exists, merge items into it
        $existingIndex = null;
        foreach ($this->menus as $index => $menu) {
            if ($menu['header'] === $header) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Merge items and roles into existing header group
            $this->menus[$existingIndex]['items'] = array_merge(
                $this->menus[$existingIndex]['items'],
                $items
            );
            // Merge roles (union)
            $this->menus[$existingIndex]['roles'] = array_unique(array_merge(
                $this->menus[$existingIndex]['roles'],
                $roles
            ));
            // Use the lowest order
            $this->menus[$existingIndex]['order'] = min(
                $this->menus[$existingIndex]['order'],
                $order
            );
        } else {
            $this->menus[] = [
                'header' => $header,
                'roles'  => $roles,
                'order'  => $order,
                'items'  => $items,
            ];
        }
    }

    /**
     * Get all registered menus sorted by order.
     *
     * @return array
     */
    public function getMenus(): array
    {
        $menus = $this->menus;
        usort($menus, fn($a, $b) => $a['order'] <=> $b['order']);
        return $menus;
    }

    /**
     * Get menus filtered for the currently authenticated user's roles.
     *
     * @return array
     */
    public function getMenusForUser(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        return array_values(array_filter($this->getMenus(), function ($menu) use ($user) {
            // If no roles specified, show to all authenticated users
            if (empty($menu['roles'])) {
                return true;
            }
            return $user->hasRole($menu['roles']);
        }));
    }
}
