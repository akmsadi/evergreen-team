<?php

if (!function_exists('scoreboard_encode')) {
    /**
     * Encode a match ID into a short opaque base-36 hash for public URLs.
     * XOR with a fixed key makes sequential IDs look random.
     */
    function scoreboard_encode(int $id): string
    {
        $scrambled = $id ^ 0x5E7A4C2B;
        return ltrim(base_convert((string) $scrambled, 10, 36), '0') ?: '0';
    }
}

if (!function_exists('scoreboard_decode')) {
    /**
     * Decode a hash back to the original match ID.
     * Returns null if the hash looks invalid.
     */
    function scoreboard_decode(string $hash): ?int
    {
        if ($hash === '' || !preg_match('/^[0-9a-z]{1,13}$/', $hash)) {
            return null;
        }
        $scrambled = (int) base_convert($hash, 36, 10);
        $id = $scrambled ^ 0x5E7A4C2B;
        return $id > 0 ? $id : null;
    }
}
