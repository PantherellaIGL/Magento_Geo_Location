# Magento Geo Location

This geolocation package is fully possible to use in any project and not just for Magento as the code doesnt use any magento specific libraries. However this is written to a certain style that the Magento industry seem to expect. If you were to use it in another project much better things could be done by implimenting an ORM or even just using a package that would allow migrations for the geolocation database. I would also expect when utalising in another project a nice admin area could be developed for it.

## Usage

When using magento 1 or 2 getting it up and working it will be slighlty different for each version of magento.

### 3rd Party requirements

This library uses http://ipstack.com so you will need an API key from them before continuing. Go and sign up.

### Enviroment Variables

You need to set some enviroment variables in you vhost. If your using apache then your vhost can have the following things added like below, nginx will be slightly different.

```
    SetEnv GEO_HOST database-host
    SetEnv GEO_DB database-name
    SetEnv GEO_USER database-username
    SetEnv GEO_PASS database-password
    SetEnv GEO_API_KEY ip-stack-api-key
    SetEnv GEO_BRAND brand-name
```

### Database setup

Create a database on you mysql server and make sure you have a user which can access it. Next using you client of choice run the contents of the ```.sql``` file provided in the repo on the new database. This will create the required structure.

You will need to populate the data in the database, for example the geostores table a row could consist of:

- id = 1, store_domain = google.co.uk, store_code = guk

Remember ```store_domain``` should NOT have http:// or www. preceeding the domain

An example for the geomapping table could be as follows:

- id = 1, brand = google, country_code = GB, store_id = 1

This should allow you to make a selection of a few stores, and a big list of countries which direct to that store. For instance all EU countries in your list would direct to the same store which could be storename.eu

### Magento 2

- Clone this repo into a Directory called ```GeoLocation``` in ```app/code/TPB``` 
- Open the file ```app/bootstrap.php```
- Look for the line of code ```require_once BP . '/app/functions.php';```
- Below that line insert the following code ```require_once BP . '/app/code/TPB/GeoLocation/functions.php'; locate();```

### Magento 1 (Untested)

NOTE: code may not autoload from the location mentioned here in magento 1, the files may need to all be manually included in the index.php file.

- Clone this repo into a Directory called ```GeoLocation``` in ```app/code/local/TPB``` 
- Open the file ```index.php```
- Look for the line of code ```umask(0);```
- Below that line insert the following code ```require_once BP . '/app/code/local/TPB/GeoLocation/functions.php'; locate();```

# Support

> - NOTE: This extension is provided under the MIT license and I do not accept any responsability for any problems you encounter when using it

If you have any questions or improvment suggestions please submit them as an issue. Any bugs you find then also please submit them as an issue with a detailed explination of how to replicate the problem and I will endevour to do my best to help.