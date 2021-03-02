<?php


namespace App\Robo;


use Robo\Tasks;

class Readme extends Tasks
{
    /**
     * Update readme
     */
    public function updateReadme()
    {
        $file = __DIR__ . '/../../readme.md';
        $date = date('Y-m-d');
        $text = <<<MD
Info
---
scoring points when parsing useragent

* os
    * name = 1
    * version = 1
    * platform = .1
* client
   * type = 1
   * name = 1
* device:
    * type = 1
    * brand = 1
    * model = 1
    
   
Before start    
---
* 1 `composer install --dev`
 
Commands  
---
* 2 `php src/robo.php init:repositories` - update all repositories
* 3 `php src/robo.php init:fixtures`     - generate paths fixtures
* 


Single run parser
---
* `php src/Parser/matomo-device-detector/parser.php --fixtures="data/paths.json"`
* `php src/Parser/whichbrowser-parser/parser.php --fixtures="data/paths.json"`


Results For {$date}
---
soon...


Who wants to contribute.
---
then...
MD;

        file_put_contents($file, $text);
    }
}