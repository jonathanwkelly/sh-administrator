# Navigation

To build navigations, Admin Architect uses https://github.com/pingpong-labs/menus.

There are 2 navigation containers available by default: sidebar menu & tools menu.

Any resource that implements `Navigable` contract will be displayed in the global (sidebar) navigation by default. 

To disable the resource from being displayed in the global navigation just don't implement that interface.

For more details about how to customize the resource appearance in the navigation please checkout the [Resources Navigation](http://docs.adminarchitect.com/Resources) documentation section.

To change default navigation structure, checkout `menu` section in the `config/administrator.php`.

This is the default navigation skeleton you may customise for your needs:

```
'menu'             => function () {
    if ($navigation = app('menus')) {
        /**
         * Sidebar navigation
         */
        $navigation->create(Navigable::MENU_SIDEBAR, function (MenuBuilder $sidebar) {
            // Dashboard
            $sidebar->route('scaffold.dashboard', trans('administrator::module.dashboard'), [], 1, [
                'id'   => 'dashboard',
                'icon' => 'fa fa-dashboard',
            ]);

            // Create new users group
            $sidebar->dropdown(trans('administrator::module.groups.users'), function ($sub) {
            	// create user shortcut
                $sub->route(
                	'scaffold.create', 
                	trans('administrator::buttons.create_item', ['resource' => 'User']), 
                	['module' => 'users'], 1, []
                );
            }, 2, ['id' => 'groups', 'icon' => 'fa fa-group']);
        });

        /**
         * Tools navigation
         */
        $navigation->create(Navigable::MENU_TOOLS, function (MenuBuilder $tools) {
            $tools->url(
                'admin/logout',
                trans('administrator::buttons.logout'),
                100,
                ['icon' => 'glyphicon glyphicon-log-out']
            );
        });
    }

    return $navigation;
},
```