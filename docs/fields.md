
# Fields

This file defines every field that you can use in `|theme|.mgcfg`.
For how to use the fields in a template see [Fields access](fields_access.md)

## Basic fields that are defined in the `fields` module
[`mods/fields/fields/`](../mods/fields/fields)

### Field [src](../mods/fields/field.php)

Every field accepts the following attributes (but nothing must be set):
```
name: Name
desc: Description
sett
    req: true
    max: 100
    min: 100
default: Default Value
```

### CheckBox
```
type: CheckBox
default: true|false
```

### DropDown
```
type: DropDown
options
    key: Value
```

### Editor
```
type: Editor
```

### Email
```
type: Email
```

### MultipleRepeat

Allows to repeat multiple fields.
Required field: `fields`

```
type: MultipleRepeat
addBtn: Add Fields
removeBtn: Remove Fields 
fields
    anotherField
        name: Some Text
```


### SingleRepeat

Allows to repeat one field.
Required field: `field`

```
type: SingleRepeat
addBtn: Add Field
removeBtn: Remove Field
field
    text
        name: Some Text
```

Even if you need to define a `slug` (here *text*), you will just receive an array in the template file.


### Number
```
type: Number
```

### Text

This is the default type.
Some you don't need to define `type`.

### Textarea
```
type: Textarea
```

## Media fields defined in mod `mediaint`
[`mods/mediaint/fields`](../mods/mediaint/fields)

### SingleFile
```
type: SingleFile
selectBtn: Select Image
allowed -a: jpg, png, gif
notAllowed: Only jpg, png and gif are allowed
```

### MultipleFiles
```
type: MultipleFiles
selectBtn: Select Images
allowed -a: jpg, png, gif
notAllowed: Only jpg, png and gif are allowed
```


## Page fields defined in mod `pagesint`
[`mods/pagesint/fields`](../mods/pagesint/fields)

### Navigation
```
type: Navigation
layouts -a: blog
resolve: short|full (default: short)
addBtn: Add Page
selectTitle: Select Page
selectBtn: Select
selectCancel: Cancel
```

### Page
```
type: Page
layouts -a: blog
noPage: yes (should only be set if you wan't to be able to don't choose a page)
resolve: short|full (default: short)
```