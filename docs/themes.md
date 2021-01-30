
# How to build a Theme?

Themes live in `user/themes/*`.  
To best understand how they work, you should probably look at the example theme
and lookup what you don't understand here.


## `|theme|.php` file

Every Theme needs to have this file, it extends [`mods/themes/theme.php`](../mods/themes/theme.php).  
To use the `|theme|.mgcfg` file you need to override `onInit` with:
```php
public function onInit() {
    $this->loadConfig();
}
```

Probably you will wan't to use fields so you need to override `$dependencies` with:
```php
protected $dependencies = [ 'fields', 'mediaint', 'pagesint' ];
```

Thats all that is required in `|theme|.php`.

## `|theme|.mgcfg` file

This file defines all fields that should be editable.  
The file is divided into five parts: assets, components, layouts, settings and pageCategories.  
Syntax Docs: [Magma Cfg](https://github.com/magma-lang/php-cfg)


### assets

Assets defines the css and js assets that should be minified and automatically included.  
Default extensions don't need to be written.  
At the moment there is a requirement, that the filename does **not include a dot**.  
External Js or Css files should not be defined here.

```
assets
    css -a: style.mgcss, rawcss
    js -a: main
```
*Note: the minification of js is really rudimentary so make sure every required semicolon exists*


### components

Here you can define all parts of the website that repeat in multiple layouts.  
For example a header which includes an image.  
See [fields](fields.md) for documentation on all the different fields.  
Every component needs to have a coresponding template file in `user/themes/|theme|/components/*.html`.

```
components
    header
        name: Header with an image
        fields
            headerImg
                type: SingleFile
                name: Header image
                selectText: select
                allowed -a: jpg, png, gif
                notAllowed: only jpg, png and gif images are allowed
```


### layouts

Every page on your website needs to have a layout.  
Layouts are constructed from `components` and `fields`.  
See [fields](fields.md) for documentation on all the different fields.

```
layouts
    home
        name: Home Layout
        components -a: header
        cache: true
        fields
            maxBlogs
                type: Number
                name: Max blog entries shown
```
*Note: cache is by default `false`.
Cache should only be set to `true` if the page has nothing dynamic, like containing content of other pages,
because if you save a page in the administration panel this will just invalid the cache of itself*

Every layout needs to have a coresponding template file in `user/themes/|theme|/layouts/*.html`.  
Components are not included in the template file automatically.
You need to include them with:

```
|comp 'header'|
```


### settings

Settings is a great way to define fields that are used across alot of layouts and components.  
Settings are mostly used in `headers` and `footers`.  
See [fields](fields.md) for documentation on all the different fields.

```
settings
    main
        name: Main settings
        fields
            footerText
                type: Textarea
                name: Copyrighttext
```


### pageCategories

Page categories are only used to organize pages in the administrator panel.

```
pageCategories
    blog
        pluralName: Blog entries
        singleName: Blog entry
        layouts -a: blog
```


## Template files

See the basic syntax here: [Magma Template](https://github.com/magma-lang/php-template)

There are four static variables that are mostly used in template files:

### `site` defined in [`mods/site/viewer.php`](../mods/site/viewer.php)

This variable gives you access to the main settings fields
- `name`
- `languages`
- `multilingual`


### `page` defined in [`mods/pages/page.php`](../mods/pages/page.php)

Mostly used fields:
- `lang`
- `url`
- `title`
- `keywords`
- `state`
- `publishOn`

### `ctn`

This contains every field that is defined in the layout. For example:
```
|ctn.maxBlogs|
```

### `theme`

Gives you access to your own `|theme|.php` file and [`mods/themes/theme.php`](../mods/themes/theme.php).  
Example to use a static image:
```
<img src="|theme.url('assets/img/image.jpg')|">
```

### Settings

Every setting *page* gets included as a variable. for example the settings page `main`:
```
<footer>
    <p>|esc main.footerText|</p>
</footer>
```