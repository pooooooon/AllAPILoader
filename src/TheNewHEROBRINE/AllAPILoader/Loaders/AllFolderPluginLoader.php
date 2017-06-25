<?php

namespace TheNewHEROBRINE\AllAPILoader\Loaders;

use FolderPluginLoader\FolderPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginDescription;
use pocketmine\Server;

class AllFolderPluginLoader extends FolderPluginLoader {

    public $server2;

    public function __construct(Server $server) {
        parent::__construct($server);
        $this->server2 = $server;
    }

    public function getPluginDescription($file) {
        if (is_dir($file) and file_exists($file . "/plugin.yml")) {
            $yaml = @file_get_contents($file . "/plugin.yml");
            if ($yaml != "") {
                $description = new PluginDescription($yaml);
                if (!$this->server2->getPluginManager()->getPlugin($description->getName()) instanceof Plugin and !in_array($this->server2->getApiVersion(), $description->getCompatibleApis())) {
                    $api = (new \ReflectionClass("pocketmine\plugin\PluginDescription"))->getProperty("api");
                    $api->setAccessible(true);
                    $api->setValue($description, [$this->server2->getApiVersion()]);
                    return $description;
                }
            }
        }

        return null;
    }

}