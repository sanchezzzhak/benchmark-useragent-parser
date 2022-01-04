Info
---
* scoring when parsing a useragent, categories:
* For each definition 1 point is 🏆 awarded

##### Basic nomination
| Parser Name | OS Name | Browser Name | Device Type | Scores |

##### Browser nomination
| Parser Name | Count | Browsers | Versions | Engines | Scores |
| ---- | ---- | ---- | ---- | ---- | ---- |
| matomo/device-detector | 336911 | 323641 | 289943 | 258677 | 872261 |
| mimmi20/browser-detector | 290933 | 285095 | 273298 | 279971 | 838364 |
| whichbrowser/parser | 130083 | 112777 | 81299 | 117583 | 311659 |



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

<details>
<summary>Preview:</summary>
 
![image](https://user-images.githubusercontent.com/1337066/147969697-4710707d-0ef5-49c9-be96-df03f87fe741.png)
 
</details>

