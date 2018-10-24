# rescue
code sample for Rescue Agency

# install
This code sample was create using Laravel 5.7.10/VueJS on LAMP (MySQL 5.7.23, Apache.  Per the .env file, a database named "rescue" needs to be created.  Lodash was imported to do a slight debounce on the key field.  Standard packages in the default Laravel installer are included.

php artisan migrate

npm install

ensure ./storage/files/uploads is writeable by the web server user (e.g. chown -R www-data, etc.)

php artisan key:generate

# assumptions
- User uploads pokemon.json, but can skip this step if re-loading the page
- A database named 'rescue' with user 'rescue' with these grants: create, delete, drop, insert, select

# considerations
- handling json files uploads with different structure
- handling non-json files
- sql injection/url hacking

# process
- the user uploads the file
 - if valid, presented with a search box 
 - if invalid, error text and request to try again

valid json is parsed and inserted into a MySQL table 'pokemon' that contains a json-column

# observations
After spending a bit of time on querying json columns, it turns out a standard LIKE %{keyword}% is sufficient.  There are no wildcard searches within JSON_CONTAINS() in MySQL.

This was cute and fun.


