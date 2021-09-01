Info
---
* scoring when parsing a useragent, categories:
* For each definition 1 point is 🏆 awarded

##### Basic nomination
| Parser Name | OS Name | Browser Name | Device Type | Scores |

##### Browser nomination
| Parser Name | Browser Name | Browser version | Browser engine | Scores |

##### Device nomination
| Parser Name | Device Type | Device brand | Device model | Scores |


Install 
---
| Command | Description |
| --- | --- |
| `composer install --dev`                      |                         |
| `php bin/console doctrine:migrations:migrate` | to apply all migrations | 
 
Commands  
---
| Command | Description |
| --- | --- |
| `php src/robo.php init:repositories`  | update all repositories |
| `php src/robo.php init:fixtures`      | generate paths fixtures |
| `php bin/console robbing:useragents`  | robbing new useragents to database |


Single run parser
---
* `php src/Parser/matomo-device-detector/parser.php --fixtures="data/paths.json"`
* `php src/Parser/whichbrowser-parser/parser.php --fixtures="data/paths.json"`
* `php src/Parser/mimmi20-browser-detector/parser.php --fixtures="data/paths.json"`


Results For 2021-03-04
---
soon...


Who wants to contribute.
---
then...