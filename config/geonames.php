<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default package boot settings
    |--------------------------------------------------------------------------
    |
    | Configure default package settings like loading default migrations,
    | morph mapping for models and others according to personal needs.
    |
    */

    'default_migrations' => true,

    'default_morph_map' => true,

    /*
    |--------------------------------------------------------------------------
    | Geonames resources directory
    |--------------------------------------------------------------------------
    |
    | A directory for geonames meta files and downloads.
    |
    */
    'directory' => storage_path('meta/geonames'),

    /*
    |--------------------------------------------------------------------------
    | Geonames files
    |--------------------------------------------------------------------------
    |
    | Filenames of the geonames resources.
    | More info: http://download.geonames.org/export/dump/
    |
    */
    'files' => [

        'country_info' => 'countryInfo.txt',

        'all_countries' => 'allCountries.txt',

        'alternate_names' => 'alternateNames.txt',

        'countries' => 'countries.php',

        'continents' => 'continents.php',

    ],

    /*
    |--------------------------------------------------------------------------
    | Default geonames seeders
    |--------------------------------------------------------------------------
    |
    | Override it when you are going to use custom migrations.
    |
    */
    'seeders' => [
        Nevadskiy\Geonames\Seeders\ContinentSeeder::class => Nevadskiy\Geonames\Seeders\ContinentDefaultSeeder::class,
        Nevadskiy\Geonames\Seeders\CountrySeeder::class => Nevadskiy\Geonames\Seeders\CountryDefaultSeeder::class,
        Nevadskiy\Geonames\Seeders\DivisionSeeder::class => Nevadskiy\Geonames\Seeders\DivisionDefaultSeeder::class,
        Nevadskiy\Geonames\Seeders\CitySeeder::class => Nevadskiy\Geonames\Seeders\CityDefaultSeeder::class,
        Nevadskiy\Geonames\Seeders\Translations\TranslationSeeder::class => Nevadskiy\Geonames\Seeders\Translations\TranslationDefaultSeeder::class,
    ],
];