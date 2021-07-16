# Index Page

Filtering and listing resources is one of the most important tasks for administering a web application. 

All of Admin Architect resources out of the box does support scopes, filters, pagination, export feature, single and batch actions [http://docs.adminarchitect.com/Resources].

Admin Architect provides a set of default tools for you to build a compelling interface into your data for the admin staff.

## Filters

![Filters](http://docs.adminarchitect.com/docs/images/index/filters.jpg)

Admin Architect provides a simple search implementation, allowing you to search/filter records in your application.

*searchable* - become any indexed @varchar, @boolean, @date or @datetime column

Customize the set of filters, their types and even the way they `filter` data as you wish you need.

Let's update the default filters set by declaring new ones in our <Resource> class:

```
public function filters()
{
	// optionally generate the default set of filters
	$this->scaffoldFilters();

	// use addFilter method
	$this->addFilter('name', 'text');

	// or directly merging with scaffolded filters 
	$this->filters = array_merge($this->scaffoldFilters(), [
		'name' => ['type' => 'text'],
		'year' => ['type' => 'select', 'options' => [
			2010 => 2010,
			2015 => 2015,
			2020 => 2020
		]],
		'group_id'   => \admin\filter\select(
			'Member of groups',
			$this->fetchPublicGroups(), // list of public groups
			function($query, $value) { // filter query
				return $query->whereIn(
					'users.id', 
					UserGroup::whereGroupId($value)->pluck('user_id')
				);
			}
		)
	]);

	return $this->filters;
}
```

Supported filter types: `text`, `select`, `date`, `daterange`.

For complex filters that require more control when fetching resources, you can define an optional \Closure $query attribute.

```
$this->scaffoldFilters();

...

$this->addFilter(
	'target', // name
	'select', // the
	'Target', // label
	['m' = UserRecord::$targetList, // select options
	function ($query, $value = null) 
	{
		return $query->join('user_record', function($join) use ($value) 
		{
			return $join->on('users.id', '=', 'user_record.user_id')
				->where('user_record.target', '=', $value)
		});
	}
);

return $this->filters;
```

To disable filters for specific resource - remove Filtrable interface from Resource's `implements` section or just return an empty array:

```
public function filters()
{
	return [];
}
```

## Scopes

![Scopes](http://docs.adminarchitect.com/docs/images/index/scopes.jpg)

As like as for filters feature, if Resource implements interface `Filtrable` it will parse Resource model for any available scopes.

Use scopes to create sections of mutually exclusive resources for quick navigation and reporting.

This will add a `tab` bar above the index table to quickly filter your collection on pre-defined scopes. 

In addition, if your model implements `SoftDeletes` contract some of useful scopes (like `withTrashed`, `onlyTrashed`) will be available too.

To hide a scope just add it to Resource `$hiddenScopes` array:

```
protected $hiddenScopes = ['active'];
```

 or add a `@hidden` docblock flag to the scope method

```
/**
 * @hidden
 */
public function scopeActive($query) 
{
	...
}

```
If for some reason you don't want to create new Model's scope you are able to define custom, Resource scopes, like so:

```
public function scopes()
{
	$this->scaffoldScopes();

	$this->addScope("Managers", function($query) {
		return $query->whereId(2);
	});

	return $this->scopes;
}
```

To rename model-based scope, just define it like so:

```
$this->addScope("Admins", "Admin");
```

Where the 1st argument is a label and 2nd argument is model scope name.

## Sorting

![Scopes](http://docs.adminarchitect.com/docs/images/index/sortables.jpg)

Any Admin Architect resource by default implements `Sortable` interface with single method `sortable()`.

It is smart enough to parse and analyze the resource model for any potentially "sortable" columns. Any indexed column is "sortable" by definition ...

To disable sorting by any column list this column into `unSortable` array in your Resource class:

```
protected $unSortable = ['id'];
```

To enable sorting by other columns, define `sortables` columns like so:

```
public function sortable()
{
	return [
		'id', 'email', 'name'
	];
}
```

To handle complex "sortable" column (for ex. relations, or columns from joined table) just define how to sort using Closure function:

```
public function sortable()
{
	return [
		'id', 'email', 'name',
		'phone' => function ($query, $element, $direction) {
			return $query->join('user_contacts', function ($join) {
				$join->on('users.id', '=', 'user_contacts.user_id');
			})->orderBy("users_contacts.{$element}", $direction);
		}
	];
}
```

Admin Architect also supports QueryBuilder class name notation:

```
public function sortable()
{
	return [
		'id', 'email', 'name',
		'skype' => SortBySkype::class
	];
}
```
Where SortBySkype is a simple class which implements Terranet\Administrator\Contract\QueryBuilder interface with single method ```build```:

```
namespace App\Http\Terranet\Administrator\Queries;

use Terranet\Administrator\Contracts\QueryBuilder;

class SortBySkype implements QueryBuilder
{
    private $query;

    private $element;

    private $direction;

    public function __construct($query, $element, $direction)
    {
        $this->query = $query;
        $this->element = $element;
        $this->direction = $direction;
    }

    public function build()
    {
        return $this->query->join('user_contacts', function ($join) {
            $join->on('users.id', '=', 'user_contacts.user_id');
        })->orderBy("users_contacts.{$this->element}", $this->direction);
    }
}
```

## Exportable collections

![Scopes](http://docs.adminarchitect.com/docs/images/index/exports.jpg)

Any Resource that implements `Exportable` interface allows you to export collections to a different formats.

Default are: XML, JSON, CSV

There is an easy way to customise the set of available formats:


```
public function formats()
{
	return array_push($this->scaffoldFormats(), ['pdf']);
}
```

and add format exporter by declaring `to<Format>` method:

```
public function toPdf($query)
{
	$pdf = 'code that exports collection';

	return response($pdf, 200, ['Content-Type' => 'application/pdf']);
}
```

Probably you'll need to use a 3rd party PDF rendering library to get the PDF generation you want.


## Pagination

![Scopes](http://docs.adminarchitect.com/docs/images/index/pagination.jpg)

You can set the number of records fetched by default per resources:

Default pagination perPage value is 20.

if you want to change this value just rewrite `perPage` method in your Resource class.

```
public function perPage()
{
	return 10;
}
```
