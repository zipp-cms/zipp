# Zipp CMS

Zipp is a small but powerful CMS and of course it's fast 🚀.  
It is intended for small or medium websites that are mostly built from scratch. Meaning that you should build a theme for every website and keep it as simple as possible.  
*Not like some other CMS where you need to download 20 Plugins and the administration panels looks like your desk at 4am in the morning!*

It should be **fun** and **easy** to edit your content and even to create a new theme.

## Installation

### Requirements
- PHP 7.2 or higher
- some more or less recent MYSQL version

### Server

Zipp needs to know which **hosts** (domains) are used and on what **path** you wan't to access the site.
Configure those in `user/configs/main.mgcfg`.


### Database

At the moment Zipp only support storing data in a database (only **mysql** is tested).
Create a new database and save the database credentials in `user/configs/database.mgcfg`.

If the `.htaccess` file does not exist, the tables will be created.
Also a new user `zippadmin` with the password `Password!` will be inserted.

After the tables are created you need to add three entries to the table `zipp_site`.
*Note: `zipp_` here is the prefix configured in `user/configs/database.mgcfg`.*
1. key: `theme` lang: `nulll` value: `"Example\\Example"`
2. key: `languages` lang: `nulll` value: `["en"]` (supported languages `mods/langs/allpossible.php`)
3. key: `multilingual` lang: `nulll` value: `false` (or true for multiple languages)


### Login

Now you can login at `/zipp/admin` with `zippadmin` and `Password!`.
*Note: `zipp/` is the path configured in `user/configs/main.mgcfg`.*


## Documentation

Documentation is a work in progress, but the administration panel and theses resources are a good start:

- [Themes](docs/themes.md)
- [Fields](docs/fields.md)
- Template engine syntax [Magma Template](https://github.com/magma-lang/php-template)
- Configuration syntax [Magma Cfg](https://github.com/magma-lang/php-cfg)
- Mgcss syntax [Magma CSS](https://github.com/magma-lang/php-css)
-- *Note: You don't have to use mgcss*

If something is not clear, please open a new issue.

## Todo

- [ ] Finish the admin page, to create, modifiy and delete users.
- [ ] Make a layout that allows dynamic components, this is useful to create onepager.
- [ ] Zipp should be able to update itself.
- [ ] There should be a way to activate auto backups with a database export.
- [ ] There should also be a tool to make migrations easier.

## Contribution

Don't hesitate to make a pull request or open a new issue.