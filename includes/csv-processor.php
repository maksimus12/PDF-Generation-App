<?php
/**
 * CSV Processor Class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class CSV_Processor {
    private $file_path;
    
    public function __construct($file_path) {
        $this->file_path = $file_path;
    }
    
    /**
     * Process CSV file and return data array
     */
    public function process() {
        $data = array();
        
        if (($handle = fopen($this->file_path, "r")) !== FALSE) {
            // Read the CSV header
            $header = fgetcsv($handle, 1000, ",");
            
            // Read data
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($header) == count($row)) {
                    $data[] = array_combine($header, $row);
                }
            }
            
            fclose($handle);
        } else {
            throw new Exception('Could not open CSV file');
        }
        
        if (empty($data)) {
            throw new Exception('No data found in CSV file');
        }
        
        return $data;
    }
    
    /**
     * Validate CSV file structure against required fields
     */
    public function validate($required_headers = array()) {
        if (($handle = fopen($this->file_path, "r")) !== FALSE) {
            // Read the CSV header
            $header = fgetcsv($handle, 1000, ",");
            fclose($handle);
            
            // Check required headers
            if (!empty($required_headers)) {
                $missing_fields = array();
                foreach ($required_headers as $required) {
                    if (!in_array($required, $header)) {
                        $missing_fields[] = $required;
                    }
                }
                
                if (!empty($missing_fields)) {
                    throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
                }
            }
            
            return true;
        }
        
        return false;
    }
}