GridRef v1.6 - Convert Irish/British Grid Reference points to WGS84 

# GridRef

## Description

In general, neither [Ireland](https://en.wikipedia.org/wiki/Ireland)
 nor [Great Britain](https://en.wikipedia.org/wiki/Great_Britain) use [latitude](https://en.wikipedia.org/wiki/Latitude) or [longitude](http://en.wikipedia.org/wiki/Longitude) in describing internal geographic locations. Instead [grid reference](https://en.wikipedia.org/wiki/Grid_reference) systems are in common usage.

The national grid referencing system was devised by the [Ordnance Survey](https://en.wikipedia.org/wiki/Ordnance_Survey), and is heavily used in their survey data, and in maps (whether published by the [Ordnance Survey of Ireland](https://en.wikipedia.org/wiki/Ordnance_Survey_of_Ireland), the [Ordnance Survey of Northern Ireland](https://en.wikipedia.org/wiki/Ordnance_Survey_of_Northern_Ireland) or commercial map producers) based on those surveys. Additionally grid references are commonly quoted in other publications and data sources, such as guide books or government planning documents.

OS Grid References are based on 100km grid squares identified by letter-pairs (in Great Britain) or a single letter (in Ireland), followed by digits which locate a point within the grid square, as explained by the _[Irish national grid reference system](https://en.wikipedia.org/wiki/Irish_national_grid_reference_system)_ or _[British national grid reference system](http://en.wikipedia.org/wiki/British_national_grid_reference_system)_. 6-digit references provide precision to 100m grid squares; 8 digits to 10m grid squares, and 10 digits to 1m.

If you are interested I also wrote a old perl script for mapping Ireland in grid codes (ideal for ecological survey), see [https://github.com/pseudogene/irishgrid](https://github.com/pseudogene/irishgrid). 

## Usage

Use a local http server with PHP capabilitites and copy the contant of this folder in `/gridref/` edit the header of the `index.php` file.

```php
$server = 'http://localhost/';
# to
$server = 'https://yourserver.com/';
```

access it through

```plaintext
http://localhost/gridref/
or
https://yourserver.com/gridref/
```


## Open source and future

Please feel free to edit the web interface, rewrite the script in modern language or develop a full API. Let me know I will advertise it! The code is under a GPL-3.0 License.


## History

* October 2009 - v1.5 (public service)
- July 2021 - v1.6 (source code released)
