<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelScanner
 * @package Drivezy\LaravelRecordManager\Library
 */
class ModelScanner
{

    /**
     *
     */
    public static function scanModels ()
    {
        self::loadModels(app_path() . '/Models', config('utility.app_namespace') . '\\Models');
    }

    /**
     * @param $path
     * @param $namespace
     * @return string
     */
    public static function loadModels ($path, $namespace)
    {
        $files = self::getDirectoryListings($path);
        foreach ( $files as $file ) {
            $strippedFile = str_replace('.php', '', $file);

            $pwd = $path . '/' . $strippedFile;
            $ns = $namespace . '\\' . $strippedFile;

            if ( is_dir($pwd) ) {
                self::loadModels($pwd, $ns);
                continue;
            }

            if ( !class_exists($ns) ) continue;

            $class = new $ns();
            if ( !is_subclass_of($class, Model::class) ) continue;

            $model = DataModel::where('model_hash', md5($ns))->first();
            if ( !$model ) {
                $model = DataModel::create([
                    'name'                => $strippedFile,
                    'namespace'           => $namespace,
                    'allowed_permissions' => 'rae-',
                    'table_name'          => $class->getTable(),
                ]);
            }

            ( new DictionaryManager($model) )->process();

        }
    }

    /**
     * @param $directory
     * @return array
     */
    private static function getDirectoryListings ($directory)
    {
        return array_values(array_diff(scandir($directory), array('..', '.')));

    }
}
