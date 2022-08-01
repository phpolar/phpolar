<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\DataStorage;

use Efortmeyer\Polar\Api\Model;
use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Api\DataStorage\CollectionStorageInterface;

use InvalidArgumentException;
use RuntimeException;

/**
 * Use to interact with a CSV file's data.
 */
final class CsvFileStorage implements CollectionStorageInterface
{
    /**
     * @var resource
     *
     * @internal
     */
    private static $writeFile;

    /**
     * @var resource
     */
    private static $readFile;

    private string $pathToFile;

    private static Collection $attributeConfigMap;

    public function __construct(string $pathToFile, Collection $attributeConfigMap)
    {
        $this->pathToFile = $pathToFile;
        static::$attributeConfigMap = $attributeConfigMap;
    }

    /**
     * @codeCoverageIgnore
     */
    public function __destruct()
    {
        if (is_readable($this->pathToFile) === true) {
            if (static::$readFile !== null) {
                fclose(static::$readFile);
            }
            if (static::$writeFile !== null) {
                fclose(static::$writeFile);
            }
        }
    }

    public static function getDefaultName(): string
    {
        return date("Ym") . ".csv";
    }

    /**
     * @return resource
     */
    private static function openWriteFile(string $pathToFile)
    {
        ini_set("auto_detect_line_endings", "1");
        $fileOpenResult = fopen($pathToFile, "a+");
        // @codeCoverageIgnoreStart
        if ($fileOpenResult === false) {
            throw new RuntimeException("File open error occured.");
        } else {
            static::$writeFile = $fileOpenResult;
        }
        // @codeCoverageIgnoreEnd
        return static::$writeFile;
    }

    /**
     * @return resource
     */
    private static function openReadFile(string $pathToFile)
    {
        ini_set("auto_detect_line_endings", "1");
        if (file_exists($pathToFile) === false) {
            touch($pathToFile);
        }
        $fileOpenResult = fopen($pathToFile, "r");
        // @phan-suppress-next-line PhanPossiblyFalseTypeMismatchProperty
        static::$readFile = $fileOpenResult;
        return static::$readFile;
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function createModel(string $className, array $values): Model
    {
        if (is_subclass_of($className, Model::class) === true) {
            return new $className(static::$attributeConfigMap, $values);
        } else {
            throw new InvalidArgumentException("${className} should extend " . Model::class);
        }
    }

    /**
     * @param resource $file
     * @return array<array<?string>>
     *
     * @api
     */
    private function read($file)
    {
        $lines = [];
        // first row contains headers
        $headers = fgetcsv($file);
        // read
        while (
             $headers !== false &&
            ($data = fgetcsv($file)) !== false
        ) {
            // headers -> keys
            $lines[] = $data;
        }
        return $lines;
    }

    /**
     * Saves a record to a CSV file.
     *
     * @api
     */
    public function save(Model $record): void
    {
        $row = get_object_vars($record);
        $headers = $record->getColumnNames();
        $file = static::openWriteFile($this->pathToFile);
        if (count($row) === 0) {
            return;
        }
        else if (stream_get_contents($file) === "") {
            fputcsv($file, $headers);
        }
        fputcsv($file, $row);
    }

    /**
     * Returns a list of records from a CSV file.
     *
     * @return Model[]
     * @api
     */
    public function list(string $modelClassName): array
    {
        $file = static::openReadFile($this->pathToFile);
        $lines = $this->read($file);
        // create collection of models
        return array_map(
            function ($line) use ($modelClassName) {
                $model = self::createModel($modelClassName, $line);
                $propertyNames = array_keys(get_object_vars($model));
                $propertyNamesString = join(",", $propertyNames);
                $columnNamesString = join(",", array_keys($line));
                if (count($propertyNames) !== count($line)) {
                    throw new RuntimeException(
                        "The model is missing some columns. Model properties ${propertyNamesString}, Column names: ${columnNamesString}"
                    );
                } else {
                    // convert column headers to property names
                    // assumes they have the same position
                    $fields = array_combine($propertyNames, $line);
                    // @phan-suppress-next-line PhanTypeNoAccessiblePropertiesForeach
                    foreach ($model as $prop => $v) {
                        // @phan-suppress-next-line PhanTypeArraySuspiciousNullable
                        $model->$prop = $fields[$prop];
                    }
                    return $model;
                }
            },
            $lines
        );
    }
}
