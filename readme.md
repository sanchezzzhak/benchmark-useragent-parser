Info
---
* scoring when parsing a useragent, categories:
* For each definition 1 point is 🏆 awarded

##### Browser nomination
| Parser Name | Count | Browsers | Versions | Engines | Scores |
| ---- | ---- | ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 338054 | 324185 | 332446 | 994685 |
| matomo/device-detector | 336911 | 323641 | 289943 | 258677 | 872261 |
| whichbrowser/parser | 130083 | 112777 | 81299 | 117583 | 311659 |



##### Bot nomination
| Parser Name | Count | Bots | Scores |
| ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 4057 | 4057 |
| matomo/device-detector | 336911 | 2812 | 2812 |
| whichbrowser/parser | 130083 | 804 | 804 |



##### Device nomination
| Parser Name | Count | Device types | Device brands | Device models | Scores |
| ---- | ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 342244 | 342244 | 340514 | 1025002 |
| matomo/device-detector | 336911 | 304588 | 264819 | 250600 | 820007 |
| whichbrowser/parser | 130083 | 128469 | 101326 | 120963 | 350758 |



##### OS nomination
| Parser Name | Count | OS | OS Versions | Scores |
| ---- | ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 340117 | 301464 | 641581 |
| matomo/device-detector | 336911 | 318605 | 296130 | 614735 |
| whichbrowser/parser | 130083 | 120146 | 115643 | 235789 |



##### Install 
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

