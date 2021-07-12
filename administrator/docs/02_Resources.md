# Resources

**Every Admin Architect resource by default corresponds to an Eloquent model. So before creating a resource you must first create an Eloquent model for it.**

## Create a Resource

The basic command for creating a resource is:

```
php artisan administrator:resource <name> <model>
```

Admin Architect will generate a new resource class in `app/Http/Terranet/Administrator/Modules` directory.

## Navigation

By default any new resource is auto Navigable.

To change the way how it appears in the navigation there is a set of methods provided by default

### Resource name

```
/**
 * The module title
 *
 * @return mixed
 */
public function title()
{
	return "Articles";
}


/**
 * The module singular title
 *
 * @return mixed
 */public function singular()
{
	return "Article";
}

```

### Resource url
To change the resource url redefine method url() like so:

```
public function url()
{
  return "articles";
}
```

Now, the resource will be available at /admin/articles.

### Menus

There are 2 menus available in Admin Architect: 

#### Sidebar

![Sidebar](http://docs.adminarchitect.com/docs/images/navigation/sidebar.jpg)

#### Tools menu
![Tools](http://docs.adminarchitect.com/docs/images/navigation/tools.jpg)


If you want to specify the menu container for your resource, you can do it like so:

```
/**
 * Navigation container which Resource belongs to
 *
 * Available: sidebar, tools
 *
 * @return mixed
 */
public function navigableIn()
{
    return Navigable::MENU_SIDEBAR;
}
```

Sometimes it required by a system to show/hide a resource by a condition:

```
/**
 * Add resource to navigation if condition accepts
 *
 * @return mixed
 */
public function showIf()
{
    return <condition>;
}
```

**Group resources for better user experience:**

![Groups](http://docs.adminarchitect.com/docs/images/navigation/groups.jpg)

```
/**
 * Navigation group which module belongs to
 *
 * @return string
 */
public function group()
{
    return "Projects";
}
```

Set the resource position inside group or entire menu (set config/menus.php ordering to true before):

```
/**
 * Resource order number
 *
 * @return int
 */
public function order()
{
    return 3;
}
```

Make navigation beautiful by assigning amazing link icons [font-awesome or ion icons available]:

```
/**
 * Attributes assigned to <a> element
 *
 * @return mixed
 */
public function linkAttributes()
{
    return ['icon' => 'fa fa-circle-o', 'id' => $this->url()];
}
```

## Index Screen

Admin Architect makes any resource available by default for listing. Any $fillable, indexed and $dates columns will be available for that by default.

Adding new columns is simple, just let the Admin Architect to know what to show and how.

Note: .dot notation also works while referencing the relationship columns;

In many cases you will probably leave resources untouched, but sometimes complex resources needs customization.


### Columns customization

![Customizing Columns](http://docs.adminarchitect.com/docs/images/index/columns.jpg)

Admin Architect will propose the default (auto-discovered) set of columns for publishing at index screen. 

You can always change the default look & feel by customizing any column in a set.

This is how the `<Resource>::columns()` method may look like

```
public function columns()
{
    return array_merge($this->scaffoldColumns(), [
    	'name' => [
    		'output' => function ($row) { return '<strong>(:name)</strong>' },
    		'standalone' => true
    	],
    	'email' => ['output' => function ($row) {
    		return '<a href="mailto:'.($e = $row->email).'">'.$e.'</a>';
    	}],
    	'Contacts' => [ // group of elements
            'elements' => [
                'user_contacts.phone', // assuming that user_contacts is a HasOne relation
                'user_contacts.skype',
                'user_contacts.icq'
            ]
        ],
        'Posts' => ['output' => function ($row) { // custom, guest-column that is not from resource model
            if ($posts = $row->posts->pluck('title', 'id')) {
                return admin\output\ul($posts, function($post) {
                    return link_to(route('scaffold.index', ['module' => 'posts']), "Posts");
                });
            }
        }]
    ]);
}
```

*available options:* 

* output - callback function or string representation,
* title - column's header
* standalone - show only column value, without label


### Presenters

![Presenters](http://docs.adminarchitect.com/docs/images/index/presenters.jpg)

Customizing each column is a good idea, but it can by annoying for complex resources, also it leaves untouched the View Resource & Relations pages.

There is another (recommended) way to customise resources in Admin Architect, called `Presenters`;

`Presenter` - is a Model's public method prefixed with `present` word and succeeded with column name in a `studly_case` column name:

for example, for model User: 
* for column `email` presenter will be `User::presentEmail()`,
* `birth_date` will look for `User::presentBirthDate()` 
* etc...

```
class User extends Model
{
	protected $fillable = ['...'];

	...
	public function presentEmail()
	{
		return '<a href="mailto:'.($e = $row->email).'">'.$e.'</a>'
	}
	
	public function presentWebSite()
    {
        return ($s = $this->attributes['web_site']) ? link_to($s, $s, ['target' => '_blank']) : null;
    }
	...
}
```

It can be very useful to extract presenters into a Trait, something like UserPresenters to leave your model clean:

```
trait UserPresenter
{
    public function presentEmail()
    {
        return link_to("mailto:" . ($e = $this->attributes['email']), $e);
    }

    public function presentWebSite()
    {
        return ($s = $this->attributes['web_site']) ? link_to($s, $s, ['target' => '_blank']) : null;
    }

    public function presentBirthDate()
    {
        return ($d = $this->attributes['birth_date'])
            ? \admin\output\label(Carbon::parse($d)->toFormattedDateString(), 'bg-gray')
            : null;
    }
    ...
    
}
```

```
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
	use UserPresenter;

	...
}
```

## Single Actions

![Single actions](http://docs.adminarchitect.com/docs/images/index/single_actions.jpg)

All CRUD (Create, Read, Update, Delete) actions are enabled by default for every single model in a resource. 

All of these actions can be enabled or disabled. Sometimes you'll need to have access to something more then just CRUD actions. 

For instance: maybe you'll wish to activate or lock some users, view project reports, report emails as spam, etc...

To add new actions or even extend default, just create a new action class (Keep the name of Action class the same as for Resource): 

```
php artisan administrator:action <Resource>
```

Admin Architect will generate new Actions class for your resource located by default in `App\Http\Terranet\Administrator\Actions`

### 1. Callback actions

Callback actions gives you ability to create a callback functions which receives as a parameter selected model, at this moment you are free to use the model in the way you need.

```
/**
 * Activate the user
 *
 * @action callback
 * @param User $user
 * @return mixed
 */
public function lock(User $user)
{
	// return $user->fill(['active' => 0])->save();
    return $user->setActivated(false);
}

/**
 * Lock the user
 *
 * @action callback
 * @param User $user
 * @return mixed
 */
public function activate(User $user)
{
	// return $user->fill(['active' => 1])->save();
    return $user->setActivated(true);
}
```

`Note:` take a look at the docblock's flag `@action callback` which identifies `callback` action

### Links

As like as callback actions Admin Architect provides an additional type of actions: `links`

```
/**
 * @action link
 * @param User $user
 * @return mixed
 */
public function viewProfile(User $user)
{
    return link_to_route('user.profile', "View Profile", ['user' => $user]);
}
```

`Note:` take a look the docblock's flag `@action link` which identifies `link` action

### Action Authorisation

Admin Architect provides a simple way to organize authorization logic and control access to resources. 

The simplest way to determine if a user may perform a given action is to define an "ability" declaring the `can` method.

Within our `abilities`, we will determine if the logged in user has the ability to delete, activate, lock any other user:

```
/**
 * Delete permission
 *
 * @param User $editor
 * @param User $user
 * @return bool
 */
public function canDelete(User $editor, User $user)
{
    return $editor->isSuperAdmin() && !$user->isSuperAdmin();
}

/**
 * Lock permission
 *
 * @param User $editor
 * @param User $user
 * @return bool
 */
public function canLock(User $editor, User $user)
{
    return $editor->isSuperAdmin() && $user->activated() && !$user->isSuperAdmin();
}

/**
 * Activate permission
 *
 * @param User $editor
 * @param User $user
 * @return bool
 */
public function canActivate(User $editor, User $user)
{
    return $editor->isSuperAdmin() && !$user->activated();
}

/**
 * ViewProfile permission
 *
 * @param User $editor
 * @param User $user
 * @return bool
 */
public function canViewProfile(User $editor, User $user)
{
    return $editor->isSuperAdmin() && $user->hasRole('member');
}
```

## Batch Actions

![Batch actions](http://docs.adminarchitect.com/docs/images/index/batch_actions.jpg)

Along of single actions Admin Architect provides a very simple way to manage collections of items.

Every resource has default ability (batch action) as "Remove selected items".

But You are free to add your batch actions as like other single action, just marking it with `@global` flag within docblock comment.

```
/**
 * Remove selected collection
 *
 * @param       $eloquent
 * @param array $collection
 * @global
 * @return $this
 */
public function removeSelected($eloquent, array $collection = [])
{
	return $eloquent->newQueryWithoutScopes()
		->whereIn('id', $collection)
		->get()
		->each(function ($item) {
			return $this->authorize('delete', $item)
				? $this->delete($item)
				: $item;
		});
}
```

