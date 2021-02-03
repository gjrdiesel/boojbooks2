# Boojbooks

- Connect to a publicly available API - [See OpenLibrary](app/Service/OpenLibrary.php) / [Usage](app/Http/Controllers/SearchController.php) 
- Create Postman collection and Vue app OR Laravel App - [Postman docs](https://documenter.getpostman.com/view/14448196/TW71n7i9)
- Add or remove items from the list 
- Change the order of the items in the list
- Sort the list of items
- Display a detail page with at least 3 points of data to display 
- Include unit tests [Did feature test](tests/Feature/HandleListOfBooksTest.php)
- Deploy it on the cloud - be prepared to describe your process on deployment https://boojbooks2.herokuapp.com/login

See original: [Booj Reading List](https://github.com/ActiveWebsite/boojbooks/blob/f0a437211cb90a50278b10e7bbaee4df785578b5/README.md).

# Testing

See [tests/Feature/HandleListOfBooksTest.php](tests/Feature/HandleListOfBooksTest.php), or visit the [postman docs](https://documenter.getpostman.com/view/14448196/TW71n7i9).

# Deployment

Deploying to Heroku because it's basically a one-liner. Have experience deploying to vapor, aws, onprem (via ansible), digitalocean.

Heroku is the best for these little test projects though :)

## Steps
```
# Install the heroku-cli, and from the boojbooks2 directory run the following:
heroku create # Uses the Procfile
git push heroku master
heroku config:set APP_KEY=$(php artisan key:generate --show) 
heroku config:set APP_DEBUG=true # or false if in production ;)
heroku addons:create jawsdb # MySQL database (config/database.php has been modified to work with default jawsdb env)

heroku run bash # Let's run the first migrations

# Now from inside heroku run bash
composer install --dev # So we can run the seeder
php artisan migrate --seed --force

heroku open
```
