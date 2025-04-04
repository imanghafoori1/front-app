### Rerfactors:
- Route model binding
- Form request classes
- Invokable controllers
- Decorator pattern for caching
- Singleton pattern
- `Route::prefix()` and `Route::name()`, 
- PHP promoted properties
- Removed useless class constructor
- Extract blade partials
- Use `Http` facade instead of `Curl`
- Used `Patch` method to update a record.

### Fixed security issues:
- Mass assignment
- File upload
- Added validation rules for price and image

### Github Actions:
- pint
- laravel-microscope check
- running tests and send coverage report to coveralls.io

### Dev tools
- Installed GrumPHP to run pint on every commit
- Installed Laravel IDE-helper

### Bug fixes:
- replace `env` calls with `config` calls to make config cache possible.

### Suggestions:
- Possibly use laravel-widgetize to cache and clean-up code.

### Tests:
- Added a ton of tests, a little bit is still missing though.
- Make structure of the application modular and folder it around it's features not type the type of classes.
- Paginate index list.
- Add more validation for user inputs.
