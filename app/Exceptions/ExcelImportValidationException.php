<?php

namespace App\Exceptions;

use Exception;

class ExcelImportValidationException extends Exception
{
    // You can pass a custom message and row number to the exception
    public function __construct($message = "", $rowNumber = null)
    {
        // Set a custom message if passed
        $message = $message ?: "Validation failed during Excel import.";

        // Include row number information if provided
        // if ($rowNumber) {
        //     $message .= " Failed on row {$rowNumber}.";
        // }

        $message['row'] = $rowNumber;
        $message = json_encode($message);

        // Pass the message to the base exception class
        parent::__construct($message);
    }
}
