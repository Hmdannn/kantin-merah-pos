
<?php
echo 'Versi PHP: ' . PHP_VERSION . '<br>';
echo 'Lokasi php.ini: ' . (php_ini_loaded_file() ?: 'Tidak ada') . '<br>';
echo 'MySQLi aktif: ' . (extension_loaded('mysqli') ? 'YA' : 'TIDAK') . '<br>';
echo 'Konstanta tersedia: ' . (defined('MYSQLI_STORE_RESULT') ? 'YA' : 'TIDAK');