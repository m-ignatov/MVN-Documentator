<?php

class CsvValidator
{
    private static $validExtensions =
    ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv'];

    public static function validate($file)
    {
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('File size is too large');
            default:
                throw new Exception('File validation error');
        }

        if ($file['size'] > 1000000) {
            throw new Exception('File size is too large');
        }
        if (!in_array($file['type'], self::$validExtensions)) {
            throw new Exception('File must be in CSV format');
        }
        if (self::isInvalidData($file)) {
            throw new Exception('File content is corrupted');
        }
    }

    private static function isInvalidData($file)
    {
        $lengthArray = array();
        $row = 1;

        if (($fp = fopen($file['tmp_name'], 'r')) !== FALSE) {
            while (($data = fgetcsv($fp, 1000, ',')) !== FALSE) {
                $lengthArray[] = count($data);
                $row++;
            }
            fclose($fp);
        }

        $lengthArray = array_unique($lengthArray);
        if (count($lengthArray) == 1) {
            return false;
        }
        return true;
    }
}
