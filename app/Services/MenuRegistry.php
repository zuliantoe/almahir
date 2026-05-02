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
            // Ensure items inherit roles from the registration if not explicitly defined
            $mergedItems = array_map(function($item) use ($roles) {
                if (empty($item['roles'])) {
                    $item['roles'] = $roles;
                }
                return $item;
            }, $items);

            $this->menus[$existingIndex]['items'] = array_merge(
                $this->menus[$existingIndex]['items'],
                $mergedItems
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
            // Ensure items inherit roles from the registration if not explicitly defined
            $processedItems = array_map(function($item) use ($roles) {
                if (empty($item['roles'])) {
                    $item['roles'] = $roles;
                }
                return $item;
            }, $items);

            $this->menus[] = [
                'header' => $header,
                'roles'  => $roles,
                'order'  => $order,
                'items'  => $processedItems,
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

        $filteredMenus = [];

        foreach ($this->getMenus() as $menu) {
            // Check top-level role
            if (!empty($menu['roles']) && !$user->hasRole($menu['roles'])) {
                continue;
            }

            if (!empty($menu['items'])) {
                $filteredItems = [];
                foreach ($menu['items'] as $item) {
                    if (!empty($item['roles']) && !$user->hasRole($item['roles'])) {
                        continue;
                    }

                    if (!empty($item['children'])) {
                        $filteredChildren = array_filter($item['children'], function ($child) use ($user) {
                            if (empty($child['roles'])) {
                                return true;
                            }
                            return $user->hasRole($child['roles']);
                        });
                        
                        if (empty($filteredChildren)) {
                            continue; // Skip item if all children are filtered out
                        }
                        $item['children'] = array_values($filteredChildren);
                    }

                    $filteredItems[] = $item;
                }

                if (empty($filteredItems)) {
                    continue; // Skip section if all items are filtered out
                }
                $menu['items'] = $filteredItems;
            }

            $filteredMenus[] = $menu;
        }

        return $filteredMenus;
    }
}
