LaravelSupports

## Settings

### Dependency
[spatie/laravel-view-models](https://github.com/spatie/laravel-view-models)

```shell
composer require spatie/laravel-view-models
```

> Use all

```shell
git submodule add https://github.com/WilsonParker/LaravelSupports app/Modules/Supports
``` 

> Use Sparse

- **[Git Sparse](https://www.lesstif.com/gitbook/git-clone-20776761.html)**

```shell
git init app/Library/LaravelSupports
cd app/Library/LaravelSupports
git config core.sparseCheckout true
git remote add -f origin https://github.com/WilsonParker/LaravelSupports.git
echo "app/Libraries" >> .git/info/sparse-checkout
echo "app/Models" >> .git/info/sparse-checkout
echo "app/Controllers" >> .git/info/sparse-checkout
echo "app/Views" >> .git/info/sparse-checkout
echo "app/ViewModels" >> .git/info/sparse-checkout
git pull origin master

```

- **in comopser.json**

```json
"autoload": {
"psr-4": {
"App\\": "app/",
"LaravelSupports\\": "app/Library/LaravelSupports/app"
},
...
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
