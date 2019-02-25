<?php

namespace Yosimitso\WorkingForumBundle\Util;

use Doctrine\Common\Cache\Cache as CacheInterface;

/**
 * Class CacheManager
 * @package Yosimitso\WorkingForumBundle\Util
 */
class CacheManager
{
    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var CacheInterface
     */
    protected $cacheDriver;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $keyPrefix;

    const TYPE_REDIS = 'redis';

    const TTL_FORUM = 3600;


    /**
     * CacheManager constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
       if (is_null($config['enable']) || !$config['enable']) {
           $this->enabled = false;
       } else {
           if ($this->initializeCache($config['type'], $config['params'])) {
               $this->enabled = true;
           } else {
               $this->enabled = false;
           }
       }
    }

    /**
     * Initialize the cache driver according to cache type
     * @param string $type
     * @param array $params
     * @return bool
     */
    protected function initializeCache(string $type, array $params)
    {
        switch ($type) {
            case self::TYPE_REDIS:
                $redis = new Redis();
                if (!$redis->connect($params['host'], $params['port'])) {
                    trigger_error('YosimitsoWorkingForumBundle: unable to connect to Redis Server', E_USER_WARNING);
                    return false;
                }
                $this->cacheDriver = new \Doctrine\Common\Cache\RedisCache();
                $this->cacheDriver->setRedis($redis);
                break;
            default:
                return false;
                break;
        }

       $this->keyPrefix = $params['key_prefix'];
       $this->type = $type;
       return true;
    }

    /**
     * Save a key/data
     * @param $key
     * @param $data
     * @return bool
     */
    public function save($key, $data, $ttl = 0)
    {
        if ($this->enabled) {
            if (!$this->cacheDriver->save($this->keyPrefix.$key, $data, $ttl)) {
                trigger_error('YosimitsoWorkingForumBundle: failed to save data into '.$this->type.' cache', E_USER_WARNING);
                return false;
            }
            return true;
        }
    }

    /**
     * Check if the cache contains a key
     * @param $key
     * @return mixed
     */
    public function contains($key)
    {
        if ($this->enabled) {
           return $this->cacheDriver->contains($this->keyPrefix.$key);
        }

        return false;
    }

    /**
     * Fetch a key from cache
     * @param $key
     * @return mixed|null
     */
    public function fetch($key)
    {
        if ($this->enabled) {
            if (!$this->contains($key)) {
                return null;
            }

            return $this->cacheDriver->fetch($this->keyPrefix.$key);
        }
        return null;
    }

    /**
     * Delete a key
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        if (!$this->cacheDriver->delete($key)) {
            $this->enabled = false; // FOR SECURITY
            trigger_error('YosimitsoWorkingForumBundle: failed to delete key from '.$this->type.' cache', E_USER_WARNING);
            return false;
        }

        return true;

    }
    /**
     * Is cache enabled ?
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}