# jessicadigital/tflcycles
A module to fetch Santander Cycle hire locations within a distance of specified locations. [Read more on the blog.](http://jessica.digital/tfl-cycle-points-php-package/)

# Installation

To include this package in your project, use composer:

```composer install jessicadigital/tflcycles```

# Fetching hire locations

To fetch locations, first create a new instance of the TflCycles class:

```$tflcycles = new \JessicaDigital\TflCycles\TflCycles(); ```

Now, specify your location(s) to find nearby hire stations:

```$tflcycles->addLocation($latitude, $longitude); ```

You must specify one or more locations.

Next, set the search distance (in metres), e.g.

```$tflcycles->setDistance(2000); ```

for 2km. This will find all bike hire locations within 2km of *any* of the specified locations. The default is 1000m.

Now, perform a search. Use the ```find()``` method to return an array of stations, or ```save($filename)``` to save this array directly to a file.

# Example

This example will fetch all cycle hire stations within 500m of Euston and Waterloo rail stations and save to a file called ```cycles.json```:

```
// Create object
$tflcycles = new \JessicaDigital\TflCycles\TflCycles();

// Add location - Euston Station
$tflcycles->addLocation(51.5290371, -0.1368696);

// Add location - Waterloo Station
$tflcycles->addLocation(51.5031686, -0.1144991);

// Change distance to 500m
$tflcycles->setDistance(500);

// Save the found locations to a file called cycles.json
$tflcycles->save('cycles.json');
```

# Usage

This package uses the [TfL Santander Cycle API](https://api.tfl.gov.uk/). Please abide by their Terms and Conditions.

Do not make repeated requests to the API - the data is only updated every five minutes. The recommended approach for using this package is to set up a scheduled CRON job to fetch and cache the data you need.

This project has no connection with TfL or the bike hire scheme; it was created as part of a development project and released for the community to enjoy.
