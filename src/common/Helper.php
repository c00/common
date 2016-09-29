<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 31/03/2016
 * Time: 01:10
 */

namespace c00\common;
use c00\dmc\DependencyContainer;
use c00\dmc\Challenge;
use c00\dmc\Answer;
use c00\dmc\Team;

/**
 * Class Helper
 * @package c00\common
 */
class Helper
{
    public static function deleteUnusedChallengeImages(DependencyContainer $dc){
        $challenges = $dc->getDb()->getChallenges(false, true);
        $folder = $dc->getSettings()->challengePictureFolder;

        $filesToKeep = [];
        foreach ($challenges as $challenge) {
            /** @var Challenge $challenge */

            //Get Challenge files
            if ($challenge->image){
                $oldImages = self::getResizedImageFilenames($challenge->image);
                $oldImages['image'] = $challenge->image;

                $filesToKeep = array_merge($filesToKeep, array_values($oldImages));
            }
        }

        //get Answer files
        $answers = $dc->getTm()->getAllAnswers();
        foreach ($answers as $answer) {
            /** @var $answer Answer */
            if ($answer->image) $filesToKeep[] = $answer->image;
        }

        $filesAvailable = array_diff(scandir($folder), ['..', '.', 'index.html']);

        $filesToRemove = array_diff($filesAvailable, $filesToKeep);
        foreach ($filesToRemove as $file) {
            if (file_exists($folder.$file)) unlink($folder.$file);
        }
    }

    public static function deleteUnusedProfileImages(DependencyContainer $dc){
        $teams = $dc->getTm()->getTeams();

        $folder = $dc->getSettings()->profileFolder;

        $filesToKeep = [];
        foreach ($teams as $team) {
            /** @var Team $team */
            if ($team->image) $filesToKeep[] = $team->image;
        }

        $filesAvailable = array_diff(scandir($folder), ['..', '.', 'index.html']);

        $filesToRemove = array_diff($filesAvailable, $filesToKeep);
        foreach ($filesToRemove as $file) {
            if (file_exists($folder.$file)) unlink($folder.$file);
        }
    }

    /** Create resized square images in desired sizes
     *
     * @param string $src The full path of the source image.
     * @param string $dstPath Destination path with trailing slash
     * @param int|array $sizes The desired size or sizes. If array, will create multiple images.
     * @return bool
     */
    public static function createSmallerSquareImages($src, $dstPath, $sizes = [500, 800]){
        if (!file_exists($src)) return false;

        if (!is_array($sizes)) $sizes = [$sizes];

        $im = new ImageManipulator();
        $filename = pathinfo($src, PATHINFO_FILENAME);
        $extension = "." . pathinfo($src, PATHINFO_EXTENSION);

        foreach ($sizes as $size) {
            $resizedFilename = $dstPath. $filename . "_" . $size . $extension;
            if (file_exists($resizedFilename)) continue;

            $im->setImageFile($src)
                ->cropToSquare()
                ->resample($size, $size)
                ->save($dstPath. $filename . "_" . $size . $extension);

        }

        return true;
    }

    /** returns a list of resized image filenames
     *
     * @param $basename string The source filename (no path)
     * @param array|int $sizes The size or sizes to get the filenames for
     * @return array|string The array of filenames if multiple. String of Filename otherwise
     */
    public static function getResizedImageFilenames($basename, $sizes = [500, 800]){
        if (!is_array($sizes)) $sizes = [$sizes];
        $filename = pathinfo($basename, PATHINFO_FILENAME);
        $extension = "." . pathinfo($basename, PATHINFO_EXTENSION);

        $array = [];
        foreach ($sizes as $size) {
            $array["image_$size"] = $filename . "_" . $size . $extension;
        }

        if (count($array) == 1) return $array[0];

        return $array;
    }

    public static function getArrayValue($array, $value, $default = ""){
        if (!is_array($array)) return $default;

        if (!isset($array[$value])) return $default;

        return $array[$value];
    }

    /**
     * Checks if an array has a list of properties set.
     *
     * @param array $a the array to check.
     * @param array $properties The list of properties it should have.
     * @return bool True if okay, False otherwise.
     */
    static function hasProperties($a, array $properties){
        if (!is_array($a)) return false;

        foreach ($properties as $p){
            if (!isset($a[$p])) return false;
        }

        return true;
    }

    /**
     * Copies a property from an array into an object (if it exists in the array).
     *
     * @param $from array The array with properties to be copied.
     * @param $to object The object with, by reference.
     * @param $property string the name of the property to be copied.
     * @return bool False if array didn't have the property. Otherwise true.
     */
    public static function copyArrayPropertyToObject(array $from, &$to, $property){
        //This copies the property of one array into the other array.
        if (!isset($from[$property])){
            return false;
        }

        $to->$property = $from[$property];
        return true;
    }

    /**
     * Copies properties from an array into an object (if it exists in the array).
     *
     * Properties will be gotten from class definition.
     *
     * @param $from array The array with properties to be copied.
     * @param $to object The object with, by reference..
     * @return bool False if array didn't have the property. Otherwise true.
     */
    public static function copyArrayPropertiesToObject(array $from, &$to){
        $result = true;

        $class_vars = get_class_vars(get_class($to));

        foreach ($class_vars as $name => $value) {
            if (!self::copyArrayPropertyToObject($from, $to, $name)) $result = false;
        }

        return $result;
    }

    /**
     * Will convert an object into an array.
     *
     * In the process it will clean out anything that's not defined in the class definition.
     * @param $object object Any object you want converted.
     * @param $keepNulls bool Defines if null values will be removed from the result. Defaults to true.
     * @return array
     */
    public static function objectToArray($object, $keepNulls = false){
        $result = [];
        foreach(get_class_vars(get_class($object)) as $key => $value){
            if((!isset($object->$key) || $object->$key === null) && !$keepNulls) {
                //Go to the next one.
                continue;
            }

            //toShowable on other objects as well.
            if (is_object($object->$key) && $object->$key instanceof IDatabaseObject){
                $result[$key] =$object->$key->toShowable();
            }else {
                $result[$key] = $object->$key;
            }


        }
        return $result;
    }

    /** Return an array of IDatabaseObjects as Showable arrays.
     * @param array $a The objects in an array
     * @return array The showable array.
     * @throws \Exception when there's no IDatabaseObject interface
     */
    public static function toShowables(array $a){
        $result = [];
        foreach ($a as $object) {
            if (is_object($object) && $object instanceof IDatabaseObject){
                $result[] = $object->toShowable();
            } else {
                throw new \Exception("I need IDatabaseObjects!");
            }

        }

        return $result;
    }

    /**
     * Will copy the value of MongoId ['_id']->{'$id'} to ['id'].
     *
     * @param array $object by reference
     * @param bool $unsetMongoId Indicates to remove the '_id'
     * @return bool|array The resulting array or false on failure
     */
    public static function fixMongoIdToString(array &$object, $unsetMongoId = false){
        if (!$object) return false;

        if (isset($object['_id'])){
            $object['id'] = $object['_id']->{'$id'};
            if ($unsetMongoId) unset ($object['_id']);

            return $object;
        }

        return false;
    }

    /** Transform an array of objects into an associative array of objects.
     *
     * @param array $array The array of objects
     * @param $assocKey string The property to use as key
     * @param bool $ignoreMissingProperty Ignore objects that don't have the $assocKey property. Will throw exceptions on false.
     * @throws \Exception When array doesn't have objects, or when key is not found in Object
     * @return array The resulting assoc array of objects.
     */
    public static function arrayOfObjectsToAssocArray(array $array, $assocKey, $ignoreMissingProperty = true){
        $result = [];

        foreach ($array as $object) {
            if (!is_object($object)){
                throw new \Exception("Not an object.");
            }
            if (!isset($object->$assocKey) && $ignoreMissingProperty){
                continue;
            } elseif (!isset($object->$assocKey) && !$ignoreMissingProperty){
                throw new \Exception("Missing property $assocKey in Object.");
            }

            $result[$object->$assocKey] = $object;
        }

        return $result;
    }

    /** Attempts to convert an array into an object.
     * @param $array array The array to convert
     * @param $type string|object The class name or an instance of the class to convert to
     * @return bool|object The object, or false on failure.
     */
    public static function objectFromArray($array, $type){
        $className = (is_object($type)) ? get_class($type) : $type;
        
        if (!class_exists($className)) return false;


        $object = new $className();
        if (!is_array($array)) return false;

        $class_vars = get_class_vars($className);
        foreach ($class_vars as $name => $item) {
            if (isset($array[$name])) $object->$name = $array[$name];
        }

        return $object;
    }
}