<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-14
 * 
 * Image Helper
 * 
 * Helper functions for image handling
 */

if (!function_exists('base64_to_image')) {
    /**
     * Convert base64 to image file and save it
     * 
     * @param string $base64_string The base64 string to convert
     * @param string $output_path The path where to save the file
     * @param string $prefix Optional prefix for filename
     * @param string $filename Optional filename without extension
     * @return string|false The filename if successful, false on failure
     */
    function base64_to_image($base64_string, $output_path, $prefix = '', $filename = '')
    {
        try {
            // Check if directory exists, create if not
            if (!is_dir($output_path)) {
                if (!mkdir($output_path, 0777, true)) {
                    log_message('error', '[base64_to_image] Failed to create directory: ' . $output_path);
                    return false;
                }
                chmod($output_path, 0777);
            }

            // Extract image data
            $data = explode(',', $base64_string);
            $image_data = base64_decode(isset($data[1]) ? $data[1] : $data[0]);

            if ($image_data === false) {
                log_message('error', '[base64_to_image] Invalid base64 string');
                return false;
            }

            // Check if the image is broken or black
            $image = @imagecreatefromstring($image_data);
            if ($image === false) {
                log_message('error', '[base64_to_image] Image is broken or invalid');
                return false;
            }

            // Check if the image is black
            $width = imagesx($image);
            $height = imagesy($image);
            $is_black = true;
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $height; $y++) {
                    if (imagecolorat($image, $x, $y) != 0x000000) {
                        $is_black = false;
                        break 2;
                    }
                }
            }
            imagedestroy($image);

            // Check if the image is green only
            $is_green = true;
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $height; $y++) {
                    $color = imagecolorat($image, $x, $y);
                    $r = ($color >> 16) & 0xFF;
                    $g = ($color >> 8) & 0xFF;
                    $b = $color & 0xFF;
                    if ($r != 0 || $g != 255 || $b != 0) {
                        $is_green = false;
                        break 2;
                    }
                }
            }

            if ($is_green) {
                log_message('error', '[base64_to_image] Image is completely green');
                return false;
            }

            if ($is_black) {
                log_message('error', '[base64_to_image] Image is completely black');
                return false;
            }

            // Generate filename
            $name = $prefix . $filename . '.png';
            $file_path = rtrim($output_path, '/') . '/' . $name;

            // Save the file
            if (file_put_contents($file_path, $image_data) === false) {
                log_message('error', '[base64_to_image] Failed to save file: ' . $file_path);
                return false;
            }

            chmod($file_path, 0666);
            return $name;

        } catch (\Exception $e) {
            log_message('error', '[base64_to_image] ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('delete_image')) {
    /**
     * Delete image file
     * 
     * @param string $filepath The full path to the file
     * @return bool True if deleted successfully, false otherwise
     */
    function delete_image($filepath)
    {
        try {
            if (file_exists($filepath)) {
                return unlink($filepath);
            }
            return false;
        } catch (\Exception $e) {
            log_message('error', '[delete_image] ' . $e->getMessage());
            return false;
        }
    }
} 