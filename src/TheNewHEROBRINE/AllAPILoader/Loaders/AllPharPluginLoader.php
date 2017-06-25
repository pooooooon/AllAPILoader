<?php

namespace TheNewHEROBRINE\AllAPILoader\Loaders;

use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginDescription;
use pocketmine\Server;

class AllPharPluginLoader extends PharPluginLoader {

    public $server2;

    public function __construct(Server $server) {
        parent::__construct($server);
        $this->server2 = $server;
    }

    public function getPluginDescription($file) {
        $phar = new \Phar($file);
        if (isset($phar["plugin.yml"])) {
            $pluginYml = $phar["plugin.yml"];
            if ($pluginYml instanceof \PharFileInfo) {
                $description = new PluginDescription($pluginYml->getContent());
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