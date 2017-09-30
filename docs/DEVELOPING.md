# Developing

This file attempts to handle documentation for developing especially pinpointing task that can happen often.
However this cannot substitute Nette documentation (which is substantially larger), therefore only specifics and key concepts are described here.

## Often tasks

### Change in template(s)

Templates are located in `app/templates`.

Logo, header, footer can be found in `app/templates/@layout.latte`, styling in `css` folder.

On production instances the templates (`.latte` files) are compiled only once on first access
then they are `temp/cache/latte`. **Therefore when changed cache needs to be deleted.**

### Change in config

On production instance the config files (`.neon` files) are compiled into container on first access
then they are `temp/cache/Nette.Configurator`. **Therefore when changed cache needs to be deleted.**

### Having permissions problems

On production the application is executed by `www-data`, but owned by `scienceslam` user.
Therefore it may not be possible to delete cache manually, for bypassing this issue see **Delete cache**, when problems persists try to move folder
under a different name.

### Delete cache

Due to permission problems manual intervention may be needed, access through browser: https://scienceslam.muni.cz/drop-cache.php?key=<key>
Key is accessible in `app/config/config.local.neon` in key `maintenance.key`

### Opening/closing registration and tickets

Opening/closing slam registration is done in administration on the slam (event).

Ticket block can be updated in `app/templates/Page/show.latte`. After changing do not forget to delete cache.

### Changing destination

See `config.neon` `parameters.form.signup.mail`. After changing do not forget to delete cache.

### Changing page meta tags

See `config.neon` `parameters.page.*`. After changing do not forget to delete cache.

## Structure of application

This application is simple Nette application and its structure follows:

- `app/components` - visual components (reusable blocks)
- `app/config` - configuration
- `app/presenter` - presenter, URL `file/<action>` goes to `FilePresenter:action<Action>`
- `app/model` - simple object wrapping interactions with one particular table.
- `app/router` - matches incoming addresses to presenters
- `app/templates` - templates for each action, organized in `<Presenter>/<action>.latte`
- `app/utils` - utility class
- `css` - css files
- `docs` - this documentation
- `images` - publicly accessible images, managed by file manager in system. Only `design` folder should be committed.
- `js` - js files
- `libs` - dependencies, managed by Composer (https://getcomposer.org/).
- `log` - log files produced by Tracy (https://tracy.nette.org/)
- `resources/migrations` - database migrations (schema in time).
- `temp` - temporary files (`cache`, `galleries`...)

**Configuration** is composed in this order:

- `config.neon` also called the based config, contains the defaults
- `config.development.neon` included only in development mode.
- `config.local.neon` also called local config, **must not be commited in repo as it contains sensitive credentials**

**Development/production mode** is detected automatically by server client.
Mandatory HTTPS is activated only on non-localhost.

**Templates** are in Latte (https://latte.nette.org/) and after changing on production cache needs to be deleted.
There are hierarchical with `@layout.latte` which contains the whole page, into it the action template is included via
`{block content}{/block}` etc.

Whole application is triggered via `index.php` file, which redirect all requests to `bootstrap.php`
which starts dependency injection container.

## Concepts of application

### Database migrations

Database is consiting of table which changes through time. These changes are reflected in
databases migrations in `resources/migrations`. Each filename (`001_BASE.sql`) starts with three character prefix declaring order, rest is human readable name.

There is tool for executing and eviding database migrations in `app/updatedb.php` however its state is unknown (probably not used).

### Slams (event)

Slams (events) are the top-level stuff, they wrap one event which have unique **url**, name, description, date and registration closing-opening, can also declare extra styles available on the event pages.

Data are stored in table: `event`.

### Pages

Pages are uniquely identified by **url** and are having heading and content.
Each page can be standalone or under certain event (~ slam).

Pages can use block layout (see below), can be set as default page of parent event and can be hidden.

Pages can create photo gallery from given path (with auto-thumbnailing).

Pages can create meta-gallery (list of pages with gallery) in such case title photo and weight of each gallery can be specified.

Pages (plain or blocks) can be previewed before saving, this works by saving it into session
(see `PreviewStorage`) and instrumented `PagePresenter:actionPreview`, which fills data for view `show.latte`.
However it's sensitive for precise simulation due to loading event/page...

Data are stored in table: `page`.

### Blocks

Each block has layout, size, background, weight and can have link. Layouts are having other properties.

For now there are following layouts (see table `list_block_type` and its enum `ListBlockType`):

- Vertical text (centered)
- Text
- Image with heading
- Registration / ticket box

Block weight and visibility can be updated via AJAX request, for implementation simplicity the weights are only incremented/decremented
instead of sophisticated solution of swapping two elements.

Data are stored in table: `block`.

### Files

Directories specified in `parameters.uploads.dirs` can be managed by administration file manager.
Uploading of multiple files is possible, however limited by PHP upload/post limits.
Files can be (batch) deleted, previewed, downloaded...

Thumbnail images are generated to `temp/galleries` and are refreshed when image has newer modification than cached thumbnail.
By default thumbnails are generated in the size of blocks (due to meta-gallery blocks usage),
however in gallery they are shown in display size (see `config.neon` `parameters.gallery`).

### Snippets

Snippet is small reusable unit on the page uniquely identified by **key** and having given content.
This content is managable via administration.

Snippets are allowed in pages (via `.snippet` macro) but not in snippets to prevent recursion problems.

The layout expecteds this to be specified `main-menu`, `footer1`, `footer2`, `header1`, however if they are missing nothing happens.
In templates `{control snippet:render,'main-menu'}` can be used.

Data are stored in table: `snippet`.

### Users

The administration is accessible after login only, that is done on `/admin/` page. There are two roles *manager* and *admin* whose
permissions differs only in user management.

The ACL subsystem is divided into resources (`file`, `user`...), roles (`manager`, `admin`) and actions (for now only `ALL`).
Rules are defined in `Authorizator` class and checked by `User::isAllowed`. Presenter however has to define its `$resource` property
and then ensure permissions by calling `$this->perm()` in `Presenter::startup()` method to ensure permission check. For individual permission check `Presenter::can()` can be used.

Exceptions (such as public access to `File::thumbnail`) has to be declared explicitly in the `Presenter::startup()` method.

Data are stored in table: `user`.

### Texy

Page content and snippet content is interpreted by Texy (https://texy.info/cs/) to provide
easier way to enter and format input.

It usually plays nicely with HTML, however when mixed output can be unpredictable, for such cases use following block.
```
/---html
<b>Bold</b>
\---
```

Texy implementation was extended with `.snippet` macro which loads (only on pages and also non-recursive) snippet with given key:
```
.snippet
main-menu
```

The snippets macros are always removed (in cases when snippet doesn't exists as well as the snippets are not supported, i.e. in snippets).

Configuration of Texy is done in `TexyFactory` which is installed to template in `createTemplate()` method of presenters and controls.

### Google Analytics / Facebook Pixel

Can be configured in `config.local.neon` by overriding keys `parameters.google.analytics` a `parameters.facebook.analytics`.
When any of these keys are present, the `@layout.latte` will automatically append the appropriate code.

### MagnificPopup

For previews of images and all files the Magnific Popup library (http://dimsemenov.com/plugins/magnific-popup/) is used.

The initialization is done in `main.js`, for galleries (slideshow) use `.new-gallery`. For generic preview of one item use `.generic-preview`.
In cases where URL is not present directly on the clicked element install custom click handler together with something like:

```
$.magnificPopup.open({
    items: {
        src: baseUrl + path,
        type: 'iframe'
    }
});
```


## Implementation notes


**WWW dir** is currently in the project root due to current deployment environment. Also the www dir is also a home folder on production server, for that reason `.gitignore` is littered with linux home files to be ignored.

**Backups** should be done by the environment, moreover for beter responsivness of backups regular dump
of database is advised, this crontab line can be helful:

```
45 4 * * Sat mysqldump -h elrond.ics.muni.cz -u scienceslam -p<password> -B scienceslam | gzip > /home/user/slam_db_`date +'\%Y-\%m-\%d'`.sql.gz
```


**Error reporting**

Tracy has failure levels, the `exception` and `error` are the important one. Each level has its log file in `log` directory such as `exception.log`.
Most of the exceptions has associated file, such as `exception--<DATE>.html`, though these
files are only generated once for each type of exception, so when the exception happens second time the entry from `exception.log` will point to older `exception-<DATE>.html` file.

In production all errors are catched and should be logged (when not cached and ignored by the programmer). End user will see an error message or generic server error (500).

**Database access**

For access to database we use Nette Database which wraps every row into `ActiveRow`, however for our needs
wrapping into `WatchingActiveRow` (via `WatchingActiveRow::fromActiveRow`) may be needed.
This allows loading all values from forms by using `addAll` method.

Each table is accessed through descendant of abstract `DAO` which provides basic operations such as `findAll`, `find`, `create`... `save` has to be implemented to perform upsert,
for example see `Muni\ScienceSlam\Model\Block::save`.

Each DAO class is registered in DI container in `config.neon` in `services`.

**Dependency injection**

On the first run the dependency injection container is composed based on the configuration.
This means that all services are prepared (not necessarily instantiated) filled with config value etc.
Then the application is executed (via `Application`).

Dependencies in constructors and in `Presenter:inject*` methods are injected by type automatically.
Some properties are initialized via `decorator` extension, see `config.neon` section `decorator` which may seem bit tricky.

**Javascript**

Stored in `js` folder, each file is directly linked from the `@layout.latte`.

- `disableLinks.js` - this snippet disables clicking on links and buttons in order for preview to be real preview, therefore used only on `Page::preview`.
- `jquery.js` - basic jQuery for easy DOM manipulation
- `magnificPopup.js` - minified external library, initialized in `main.js`
- `main.js` - file with adhoc mini-snippets of code
- `nette.ajax.js` - code for making Nette create and handle ajax request, initialized in `main.js`, see https://github.com/vojtech-dobes/nette.ajax.js
- `netteForms.js` - code for making forms validation work in browser before sending, doesn't need separate initialization.
- `w3.js` - code for simple tasks, such as hide/show elements on toggle... See https://www.w3schools.com/w3js/

**Using AJAX**

Ajax support is built into Nette framework, however making it work the `nette.ajax.js` needs to be loaded and initialized.
Then all links and forms with CSS class `ajax` will be ajaxified and clicking/sending will perform AJAX request.
This request is processed and outcome is returned, typically after some action (saving etc.) redirection should be done,
however Nette supports invalidation of snippet (Latte macro `snippet`) which is resent to the client and only this snippet is
replaced in the page. (Function `$presenter->isAjax()` can be handy for distinguishing between the requests.)

Please use snippets wisely as they make page a bit inconsistent (something is sent normally, something is not) and create
other JS/styling/... a bit painful. And always expect non-AJAX requests as JS can fail to load/initialize/...

**Controls**

In Nette there can be reusable components called controls, see `app/components/`. Each one should have factory and interface (for pass-through
factories the factory can be auto-generated by Nette) and should be DI-injected using this interface type which is registered in `config.neon` under `services`.

Visual components should extend `VisualControl` which add capability of having templates autoobtained from the name and ensures rendering of this template.

In Latte `{control namedControl:foo,$id}` will obtain namedControl from `Presenter::createComponentNamedControl()` and will trigger `renderFoo` with parameter `$id`. The `foo` and parameters are optional, in that case only `render` is invoked.
Do not forget that custom render methods needs to call `$this->render()` in order to work as last command of the method.

**Forms**

In Nette forms should extend or instantiate `Form` (form the `UI` package) which ensures proper binding data, proper validation etc.
