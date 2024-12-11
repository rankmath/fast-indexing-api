<?php

namespace Rank_Math_Instant_Indexing;

// For older (pre-2.7.2) verions of google/apiclient
if (\file_exists(__DIR__ . '/../apiclient/src/Google/Client.php') && !\class_exists('Rank_Math_Instant_Indexing\Google_Client', \false)) {
    require_once __DIR__ . '/../apiclient/src/Google/Client.php';
    if (\defined('Google_Client::LIBVER') && \version_compare(Google_Client::LIBVER, '2.7.2', '<=')) {
        $servicesClassMap = ['Rank_Math_Instant_Indexing\Google\Client' => 'Google_Client', 'Rank_Math_Instant_Indexing\Google\Service' => 'Google_Service', 'Rank_Math_Instant_Indexing\Google\Service\Resource' => 'Google_Service_Resource', 'Rank_Math_Instant_Indexing\Google\Model' => 'Google_Model', 'Rank_Math_Instant_Indexing\Google\Collection' => 'Google_Collection'];
        foreach ($servicesClassMap as $alias => $class) {
            \class_alias($class, $alias);
        }
    }
}
\spl_autoload_register(function ($class) {
    if (0 === \strpos($class, 'Google_Service_')) {
        // Autoload the new class, which will also create an alias for the
        // old class by changing underscores to namespaces:
        //     Google_Service_Speech_Resource_Operations
        //      => Google\Service\Speech\Resource\Operations
        $classExists = \class_exists($newClass = \str_replace('_', '\\', $class));
        if ($classExists) {
            return \true;
        }
    }
}, \true, \true);
