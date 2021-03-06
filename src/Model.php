<?php

namespace AdversusPHP;

/**
 * Class Model
 *
 * @package AdversusPHP
 */
abstract class Model
{

    /**
     * @var Client
     */
    protected static $client;
    /**
     * @var string
     */
    protected static $path;
    /**
     * @var int
     */
    public $id;
    /**
     * @var array
     */
    protected $data;

    /**
     * Model constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if (!is_null($data)){
            $this->id   = $data['id'];
            $this->data = $data;
        }
        else{
            $this->data = [
                'active' => true,
                'status' => 'new'
            ];
        }
    }

    /**
     * @param Client $client
     */
    public static function init(Client $client)
    {
        static::$client = $client;
    }

    /**
     * @param $id
     *
     * @return static
     */
    public static function find($id)
    {
        $url      = '/' . static::$path . '/' . $id;
        $response = static::$client->get($url);
        $data     = json_decode($response->getBody()->getContents(), true);
        return new static($data[static::$path][0]);
    }

    /**
     * @param mixed $filters
     *
     * @return static[]
     */
    public static function all($filters = null)
    {
        $url = '/' . static::$path;
        if ($filters !== null) {
            $url .= '?filters=' . Filter::toParam($filters);
        }
        $response = self::$client->get($url);
        $data     = json_decode($response->getBody()->getContents(), true);
        $is_assoc = !empty($data) && array_keys($data) !== range(0, count($data) - 1);
        if ($is_assoc) {
            $data = $data[static::$path];
        }
        $models   = [];
        foreach ($data as $modelData) {
            $models[] = new static($modelData);
        }
        return $models;
    }


    public function dump()
    {
        if (function_exists('krumo')) {
            krumo($this->data);
        } else {
            var_dump($this->data);
        }
    }
}
