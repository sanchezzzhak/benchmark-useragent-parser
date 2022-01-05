Info
---
* scoring when parsing a useragent, categories:
* For each definition 1 point is 🏆 awarded

##### Browser nomination
| Parser Name | Count | Clients | Browsers | Versions | Engines | Scores |
| ---- | ---- | ---- | ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 338054 | 244429 | 324185 | 332446 | 1239114 |
| matomo/device-detector | 346301 | 332954 | 274717 | 299014 | 267531 | 1174216 |
| whichbrowser/parser | 346301 | 288155 | 210058 | 215506 | 299925 | 1013644 |



##### Bot nomination
| Parser Name | Count | Bots | Scores |
| ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 4057 | 4057 |
| whichbrowser/parser | 346301 | 2948 | 2948 |
| matomo/device-detector | 346301 | 2854 | 2854 |



##### Device nomination
| Parser Name | Count | Device types | Smartphones | Tables | Feature phones | Device brands | Device models | Scores |
| ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 340514 | 193607 | 44368 | 8537 | 274721 | 314040 | 929275 |
| matomo/device-detector | 346301 | 318891 | 197851 | 59724 | 6346 | 273851 | 259476 | 852218 |
| whichbrowser/parser | 346301 | 322858 | 221388 | 64188 | 14009 | 237450 | 285449 | 845757 |



##### OS nomination
| Parser Name | Count | OS | OS Versions | Scores |
| ---- | ---- | ---- | ---- | ---- |
| mimmi20/browser-detector | 346301 | 340117 | 301464 | 641581 |
| matomo/device-detector | 346301 | 335595 | 305316 | 640911 |
| whichbrowser/parser | 346301 | 301535 | 278230 | 579765 |



##### Install 
| Command | Description |
| --- | --- |
| `composer install` |     |
| `php yii` | show all available commands  |
| `php yii migrate` | to apply all migrations | 
| `php yii serve` | run web server | 
 
##### Commands  

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

##### Parsers

* ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) [mimmi20/browser-detector](https://github.com/mimmi20/browser-detector)
* ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) [matomo/device-detector](https://github.com/matomo-org/device-detector)
* ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) [whichbrowser/parser](https://github.com/WhichBrowser/Parser-PHP)