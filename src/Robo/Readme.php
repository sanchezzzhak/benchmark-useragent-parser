<?php


namespace App\Robo;


use Robo\Tasks;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\BufferedOutput;


class Readme extends Tasks
{

    private function renderTableMdStyle($tableData)
    {
        $output = new BufferedOutput();
        $style = new TableStyle();
        $style->setDefaultCrossingChar('|');

        $style->setCellHeaderFormat('<info>%s</info>');

        $table = new Table($output);
        $table->setHeaders($tableData['headers']);
        $table->setRows($tableData['rows'] ?? []);
        $table->setStyle($style);
        $table->render();
        $lines = explode(PHP_EOL, $output->fetch());
        array_shift($lines);
        array_pop($lines);
        array_pop($lines);
        return implode(PHP_EOL, $lines);
    }

    /**
     * Update readme
     */
    public function updateReadme()
    {

        $basicNominationTable = $this->renderTableMdStyle([
            'headers' => ['Parser Name', 'OS Name', 'Browser Name', 'Device Type', 'Scores']
        ]);

        $deviceNominationTable = $this->renderTableMdStyle([
            'headers' => ['Parser Name', 'Device Type', 'Device brand', 'Device model', 'Scores']
        ]);

        $browserNominationTable = $this->renderTableMdStyle([
            'headers' => ['Parser Name', 'Browser Name', 'Browser version', 'Browser engine', 'Scores']
        ]);

        $file = __DIR__ . '/../../readme.md';
        $date = date('Y-m-d');
        $text = <<<MD
Info
---
* scoring when parsing a useragent, categories:
* For each definition 1 point is awarded

##### Basic nomination
{$basicNominationTable}

##### Browser nomination
{$browserNominationTable}

##### Device nomination
{$deviceNominationTable}


[View reports online](https://sanchezzzhak.github.io/benchmark-useragent-parser/site/)  (I'm in the process...)
   
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
* `php src/Parser/mimmi20-browser-detector/parser.php --fixtures="data/paths.json"`


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