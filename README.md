# Boojbooks

For todo list; see [Booj Reading List](https://github.com/ActiveWebsite/boojbooks/blob/f0a437211cb90a50278b10e7bbaee4df785578b5/README.md).

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
