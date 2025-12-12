<?php

/**
 * Cache Class
 * Simple file-based caching system
 */

class Cache
{
    private $cacheDir = __DIR__ . '/../cache/redis/';
    private $ttl = 3600; // 1 hour default

    public function __construct($ttl = 3600)
    {
        $this->ttl = $ttl;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get($key)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        // Use JSON instead of unserialize for security
        $data = json_decode(file_get_contents($file), true);

        if ($data === null) {
            return null;
        }

        // Check expiration
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }

        return $data['value'];
    }

    public function set($key, $value, $ttl = null)
    {
        $ttl = $ttl ?? $this->ttl;
        $file = $this->getFilePath($key);

        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];

        // Use JSON instead of serialize for security
        file_put_contents($file, json_encode($data), LOCK_EX);
    }

    public function remember($key, $ttl, $callback)
    {
        $cached = $this->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    public function forget($key)
    {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function flush()
    {
        $files = glob($this->cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    private function getFilePath($key)
    {
        return $this->cacheDir . md5($key) . '.cache';
    }
}
