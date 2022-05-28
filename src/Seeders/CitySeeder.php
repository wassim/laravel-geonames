<?php

namespace Nevadskiy\Geonames\Seeders;

use Nevadskiy\Geonames\Parsers\GeonamesDeletesParser;
use Nevadskiy\Geonames\Parsers\GeonamesParser;
use Nevadskiy\Geonames\Services\DownloadService;

class CitySeeder extends ModelSeeder
{
    /**
     * The city model class.
     *
     * @var string
     */
    protected static $model = 'App\\Models\\Geo\\City';

    /**
     * The minimum population filter.
     *
     * @var int|null
     */
    protected $minPopulation;

    /**
     * The allowed feature codes.
     *
     * @var array
     */
    protected $featureCodes = [];

    /**
     * The country resources.
     *
     * @var array
     */
    protected $countries;

    /**
     * The division resources.
     *
     * @var array
     */
    protected $divisions;

    /**
     * Make a new seeder instance.
     */
    public function __construct()
    {
        $this->featureCodes = config('geonames.filters.cities.feature_codes');
        $this->minPopulation = config('geonames.filters.cities.min_population');
    }

    /**
     * Use the given city model class.
     */
    public static function useModel(string $model): void
    {
        static::$model = $model;
    }

    /**
     * Get the city model class.
     */
    public static function model(): string
    {
        return static::$model;
    }

    /**
     * {@inheritdoc}
     * @TODO: refactor with DI downloader and parser.
     */
    protected function getRecords(): iterable
    {
        $path = resolve(DownloadService::class)->downloadAllCountries();

        foreach (resolve(GeonamesParser::class)->each($path) as $record) {
            yield $record;
        }
    }

    /**
     * {@inheritdoc}
     * @TODO: refactor with DI downloader and parser.
     */
    protected function getDailyModificationRecords(): iterable
    {
        $path = resolve(DownloadService::class)->downloadDailyModifications();

        foreach (resolve(GeonamesParser::class)->each($path) as $record) {
            yield $record;
        }
    }

    /**
     * {@inheritdoc}
     * @TODO: refactor with DI downloader and parser.
     */
    protected function getDailyDeleteRecords(): iterable
    {
        $path = resolve(DownloadService::class)->downloadDailyDeletes();

        foreach (resolve(GeonamesDeletesParser::class)->each($path) as $record) {
            yield $record;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResourcesBeforeMapping(): void
    {
        $this->loadCountries();
        $this->loadDivisions();
    }

    /**
     * Load country resources.
     */
    protected function loadCountries(): void
    {
        $this->countries = CountrySeeder::newModel()
            ->newQuery()
            ->pluck('id', 'code')
            ->all();
    }

    /**
     * Load division resources.
     */
    protected function loadDivisions(): void
    {
        $this->divisions = DivisionSeeder::newModel()
            ->newQuery()
            ->get(['id', 'country_id', 'code'])
            ->groupBy(['country_id', 'code'])
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    protected function unloadResourcesAfterMapping(): void
    {
        $this->countries = [];
        $this->divisions = [];
    }

    /**
     * {@inheritdoc}
     */
    protected function filter(array $record): bool
    {
        return in_array($record['feature code'], $this->featureCodes, true)
            && $this->isPopulationAllowed($record);
    }

    /**
     * Determine if the population of the record is allowed for seeding.
     */
    protected function isPopulationAllowed(array $record): bool
    {
        if (is_null($this->minPopulation)) {
            return true;
        }

        return $record['population'] >= $this->minPopulation;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapAttributes(array $record): array
    {
        return [
            'name' => $record['asciiname'] ?: $record['name'],
            'country_id' => $this->getCountryId($record),
            'division_id' => $this->getDivisionId($record),
            'latitude' => $record['latitude'],
            'longitude' => $record['longitude'],
            'timezone_id' => $record['timezone'],
            'population' => $record['population'],
            'elevation' => $record['elevation'],
            'dem' => $record['dem'],
            'feature_code' => $record['feature code'],
            'geoname_id' => $record['geonameid'],
            'synced_at' => $record['modification date'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Get a country ID by the given record.
     */
    protected function getCountryId(array $record): string
    {
        return $this->countries[$record['country code']];
    }

    /**
     * Get a division ID by the given record.
     */
    protected function getDivisionId(array $record): ?string
    {
        return $this->divisions[$this->getCountryId($record)][$record['admin1 code']][0]['id'] ?? null;
    }
}
