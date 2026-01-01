<?php

/**
 * Caching System
 * Basic caching for improved performance
 */

class Cache {
    private static $cache_dir;
    
    public static function init() {
        self::$cache_dir = BASE_PATH . '/cache/';
        if (!is_dir(self::$cache_dir)) {
            mkdir(self::$cache_dir, 0755, true);
        }
    }
    
    /**
     * Get cached data
     */
    public static function get($key, $default = null) {
        self::init();
        $file = self::$cache_dir . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($file));
        
        // Check expiry
        if (isset($data['expires']) && $data['expires'] < time()) {
            unlink($file);
            return $default;
        }
        
        return $data['value'] ?? $default;
    }
    
    /**
     * Set cached data
     */
    public static function set($key, $value, $ttl = 3600) {
        self::init();
        $file = self::$cache_dir . md5($key) . '.cache';
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        file_put_contents($file, serialize($data));
    }
    
    /**
     * Delete cached data
     */
    public static function delete($key) {
        self::init();
        $file = self::$cache_dir . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Clear all cache
     */
    public static function clear() {
        self::init();
        $files = glob(self::$cache_dir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
    
    /**
     * Remember - Get or set
     */
    public static function remember($key, $callback, $ttl = 3600) {
        $value = self::get($key);
        
        if ($value === null) {
            $value = $callback();
            self::set($key, $value, $ttl);
        }
        
        return $value;
    }
}

// Initialize cache directory
Cache::init();
