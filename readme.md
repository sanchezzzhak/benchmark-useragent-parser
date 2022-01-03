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
| `composer install` |     |
| `php yii` | show all available commands  |
| `php yii migrate` | to apply all migrations | 
| `php yii serve` | run web server | 
 
Commands  
---
| Command | Description |
| --- | --- |
| `php yii robbing` | robbing new useragents to database |
| `php yii matomo-parser` | analyze all useragents and save result to db |
| `php yii mimmi20-parser`| analyze all useragents and save result to db |
| `php yii whichbrowser-parser` | analyze all useragents and save result to db |


After executing the above commands, you can view the results online
```
cd project & cd web/
php -S localhost:8080
```
