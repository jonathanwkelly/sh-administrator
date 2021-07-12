# Form

![Scopes](http://docs.adminarchitect.com/docs/images/form/edit.jpg)

Admin Architect gives you complete control over the output of the form by creating a thin DSL on top of Illuminate\Form package:

Defining of all fields for editing can by annoying, so Admin Architect eliminates that step: all `$fillable` columns are available for editing.

You can extend default form by adding columns or changing the column settings:

For models, each field should be one of your model's SQL columns or one of its Eloquent relationships or any custom field (assuming you know how to handle and save it). 

The order in which they are given is the order in which the admin user will see them.

```
public function form()
{
	return array_merge($this->scaffoldForm(), [
		'name' => ['type' => 'text'],
		'email' => \admin\form\email('Email', ['minlength' => 5]),
		'about' => \admin\form\tinymce('About me', []),
		'contacts.phone' => ['type' => 'tel', 'minlength' => 11],
		'contacts.skype' => ['type' => 'text', 'minlength' => 30],
		'groups.group_id' => [
			'label' => 'Member of groups',
			'type' => 'select',
           	'multiple' => true,
			'style' => 'height: 200px; width: 500px;',
			'options' => function() {
				return Group::wherePublic(1)->pluck('title', 'id');
			}
		]
	]);
}
```
`.dot` notation points to a relationship,
in case of `contacts.` - it points to a HasOne relationship `contacts`,
in case of `groups.` - it points to a BelongsToMany relationship `groups`

`Note:` checkout the src/helpers.php file for any available form helpers.

### Editable relations

Admin Architect allows you to edit OneToOne relations altogether in a single form!

Let's say your `User` model has `HasOne` record() relationship and linked with another `Record` class. It's annoying to edit them separately one of each other, right?

Just add `@editable` flag to your `record()` relationship and all the `Record::$editable` columns will be joined into a single editable form.

```
class User extends Model
{
	protected $fillable = ['id', 'name', 'email', '...'];

	/**
	 * Record relationship
	 *
	 * @editable	 
	 */ 
	public function record()
	{
		return $this->hasOne(App\Record::class);
	}	
}

class Record extends Model
{
	protected $fillable	= ['target', 'height', 'current_weight', 'target_weight'];
}
```

The result form will contain all the `$fillable` columns from both models: **User** & **Record**.

## Input types

Along with reserved keys like: `type`, `label`, `description`, `options (for type select)`  you're free to use any of html specific tags.

So for numbers you can use, `min`, `max`, `step`, etc...
For text type you'll be able to use `placeholder`, `title`, `minlenght`, `maxlength`, etc...

### Key

The key field type can be used to show the primary key's or unEditable value. 
You cannot make this field editable since primary key values are handled internally by your database.

```
'id' => ['type' => 'key'],
```

### Text

The text field type should be any text-like type in your database. text is the default field type.

```
'name' => ['type' => 'text', 'label' => 'Your name'],
```

### Password

The password field type should be any text-like type in your database. 

You should use Eloquent mutators in conjunction with a password field to make sure that the supplied password is properly hashed.

Or just enable the `manage_passwords` feature in `config/administrator.php` to enable handling this by Admin Architect.

```
'password' => ['type' => 'password'],
```

### Textarea

The textarea field type should be any text-like type in your database. In the edit form, an admin user will be presented with a textarea.
The limit option lets you set a character limit for the field. The height option lets you set the height of the textarea in pixels.

```
'bodyPlain' => ['type' => 'textarea', 'label' => 'Your name', 'cols' => 60, 'rows' => 10],
```

### Tinymce, CKEditor

The tinymce, ckeditor field types should be a TEXT type in your database.

In the edit form, an admin user will be presented with a Tinymce OR CKEditor WYSIWYG. 

When the field is saved to the database, the resulting HTML is stored in the TEXT field.

```
'bodyHtml' => ['type' => 'tinymce'],
```

### Number

The number field type should be a numeric type in your database.

In the edit form, an admin user will be presented with a text input. This text input will force your users to enter a number in the proper format.

The min, max and step options lets you set the &lt;input type="number" /&gt; attributes


```
'height' => ['type' => 'number', 'min' = 0, 'max' => 100, 'step' => 1],
```

### Boolean

The boolean field type should be represented as an integer field in your database. 

Usually schema creators allow you to choose BOOLEAN which resolves to something like TINYINT(1). 

This field will work as long as you can put integer 1s and 0s in your database field.

In the edit form, an admin user will be presented with a checkbox


```
'active' => ['type' => 'checkbox'],
```


### Select

The select field type should be any text-like type or an ENUM in your database. 

This field type helps you narrow down the options for your admin users in a data set that you know will never change. 

```
'season' => [
    'type' => 'select',
    'title' => 'Season',
    'options' => ['Winter', 'Spring', 'Summer', 'Fall']
],
```
In the edit form, an admin user will be presented with a select box.


### Date

The date and date range field types should be a DATE or DATETIME type in your database.

```
'birth_date' => [
    'type' => 'date',
    'title' => 'Birth date',
]
```

In the edit form, an admin user will be presented with a Datepicker.

### File, Image

```
'avatar' => ['type' => 'image'],
'cv' => ['type' => 'file']
```

Files & Images are handled by `codesleeve/laravel-stapler` library.

This is the shortest way to attach an image object to a column:

```
class User extends Eloquent implements StaplerableInterface {
    use EloquentTrait;

    // Add the 'avatar' attachment to the fillable array so that it's mass-assignable on this model.
    protected $fillable = ['avatar', 'cv'];

    public function __construct(array $attributes = array()) 
    {
        $this->hasAttachedFile('avatar', [
            'styles' => [
                'medium' => '300x300',
                'thumb' => '100x100'
            ]
        ]);

        $this->hasAttachedFile('cv');

        parent::__construct($attributes);
    }
}
```

For more info please checkout its documentation by accessing https://github.com/CodeSleeve/laravel-stapler.


## Relations

coming soon...

