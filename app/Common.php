<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('format_no_antrian')) {
    /**
     * Format nomor antrian menjadi #01, #02, dst.
     */
    function format_no_antrian($nomor)
    {
        $nomor = (int) $nomor;

        if ($nomor < 1) {
            return '#-';
        }

        return '#' . str_pad((string) $nomor, 2, '0', STR_PAD_LEFT);
    }
}
