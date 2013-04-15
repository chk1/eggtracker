Installation
============

## Software Requirements
* Webserver like Apache2 or Nginx
* PHP 5 with pgsql module enabled
* PostgreSQL 9.1 with Postgis (any recent version will do)

## Setup procedure
* Clone this repo to your web server's directory
* Create a spatial database and run Geosoftdatenbank.sql to create the database structure
* Copy config.inc.sample.php to config.inc.php, add your Cosm api key and database credentials there

### Config
* The latitude and longitude values in the config file specify the center point for the automated egg search (see cronjobs)
* Email address is used for contact form
* If you don't have a Cosm API key you can get one for free from their website at cosm.com

## Running eggtracker

### Adding Air Quality Eggs to your database
Run inc/query_eggs.php once manually to add eggs to the database.

### Adding Lanuv Stations to your database
Use your database tool (pgAdmin or command line tool) to add Lanuv Stations to the database manually. For cosmid use anything above 1000000. Select a station from [the Lanuv air quality station list](http://www.lanuv.nrw.de/luft/immissionen/aktluftqual/eu_luft_akt.htm). Example entry:

    cosmid   id  geom  active  about       link
    1000001  81  ...   TRUE    Lanuv MSGE  http://www.lanuv.nrw.de/luft/temes/heut/MSGE.htm

Afterwards, add the cosmid and unique identifier to the parser `cron/lanuv_parser2.php`:

    $lanuvstations = array(
	1000001 => "MSGE"
    );

### Cronjobs
Change your directories accordingly.
* Set up cron jobs for Air Quality Egg data collection

        */3 * * * * (cd /var/www/eggtracker/cron && php fetch_historic_data.php) > /var/www/eggtracker/logs/log.txt

* Set up cron jobs for Lanuv data collection

        */5 * * * * (cd /var/www/eggtracker/cron && php lanuv_parser2.php) > /var/www/eggtracker/logs/log.txt

* Set up cron jobs for data validation

        */5 * * * * (cd /var/www/eggtracker/cron && php validate.php) > /var/www/eggtracker/logs/log.txt

* Set up daily cron jobs for egg radius search

        0 0 * * * (cd /var/www/eggtracker/cron && php query_eggs.php) > /var/www/eggtracker/logs/log.txt

Periodically it can happen that the parser is stuck and won't insert new data. This is the case when the Lanuv station or Air Quality Egg have a data hole, i.e. don't deliver data for a short window of time. 
