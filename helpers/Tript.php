<?php

class Tript {
    public static function encryptString($plaintext) {

        $secret = $_ENV['TRIPT_SECRET'];

        // Genera una clave y un vector de inicialización (IV) a partir de la contraseña y el salt
        $key = hash('sha256', $secret, true);
        $iv = substr(hash('sha256', $secret), 0, 16); // Toma los primeros 16 bytes del hash del salt para el IV
    
        // Cifra el string usando AES-256-CBC
        $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, 0, $iv);
        
        // Convierte el resultado a base64 para su almacenamiento o transmisión
        return base64_encode($ciphertext);
    }
    
    public static function decryptString($ciphertext) {

        $secret = $_ENV['TRIPT_SECRET'];

        // Genera la misma clave y vector de inicialización (IV)
        $key = hash('sha256', $secret , true);
        $iv = substr(hash('sha256', $secret), 0, 16);
    
        // Decodifica desde base64 y luego descifra el texto
        $ciphertext = base64_decode($ciphertext);
        return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
    }
}