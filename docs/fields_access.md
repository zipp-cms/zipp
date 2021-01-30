
# Fields access

Mostly a field just gives you its value.
For example the `Text` field:
```html
<span>|esc ctn.field|</span>
```

This file just documents the fields that don't just return it's raw value.

### DropDown
```html
<!-- DropDown just returns the key -->
|esc ctn.field|
```

### MultipleRepeat
Definition:
```
authors
    type: MultipleRepeat
    addBtn: Add Author
    removeBtn: Remove Author 
    fields
        fullname
            name: Authors name

        lastBook
            name: Last Published Book
```
Html:
```html
<div class="authors">
|for author in ctn.authors|
    <p><b>|esc author.lastBook|</b> by |esc author.fullname|</p>
|else|
    <p>No Author</p>
|end|
</div>
```

### SingleRepeat
Definition:
```
categories
    type: SingleRepeat
    addBtn: Add Category
    removeBtn: Remove Category
    field
        cat
            name: Category
```
Html:
```html
<ul>
|for cat in ctn.categories|
    <li>|esc cat|</li>
|end|
</ul>
```

### SingleFile
Definition:
```
image
    type: SingleFile
    selectBtn: Select Image
    allowed -a: jpg, png, gif
    notAllowed: Only jpg, png and gif are allowed
```
Html:
```html
<!-- if no image is defined `ctn.image === false` -->
<!-- to check that an image is defined use: -->
|if ctn.image|
    <img src="|ctn.image.src()|">
|end|

<img src="|ctn.image.src()|" alt="|ctn.image.alt()|">
       ==
|ctn.image.tag()|

<!-- or with a custom class -->
<img class="image" src="|ctn.image.src()|" alt="|ctn.image.alt()|">
```

### MultipleFiles
*MultiplesFiles* behaviour is the same as with *SingleFile* except that you receive an array and an image will never be `false`

### Navigation
Navigation is a bit tricky, it returns a tree structure.
But of course you can just ignore the children's.
See [`user/themes/example/templ/nav.html`](../user/themes/example/templ/nav.html) for a recursive example.
```html
|for nav in ctn.navigation|
    |nav.title|
    |nav.url|
    |nav.active|
    |nav.ctn| <!-- if resolve: full -->
    |nav.layout| <!-- if resolve: full -->
    |nav.childrenActive|
    |nav.children|
    <!-- nav.children: now repeats the cycle -->
|end|
```

### Page
Return the same as the global `page` variable:
- `lang`
- `url`
- `title`
- `keywords`
- `publishOn`

if `resolve: full`  is set `page.ctn` will be available