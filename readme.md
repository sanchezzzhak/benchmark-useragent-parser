Info
---
* scoring when parsing a useragent, categories:
* For each definition 1 point is 🏆 awarded

##### Last date scan
2022/01/06
##### Total
| Parser Name | UA Count | Min time | Max time | Total time | Avg time | Min memory | Max memory | Total memory | Avg memory | Bots | Bot uniques | OS | OS versions | Client types | Client names | Client versions | Engine names | Engine versions | Device types | Brand names | Model names | Model unique names
| ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
|matomo/device-detector| 346301| 0.0| 0.5067| 1019.66| 0.0029| 96 byte| 6.24 Mb| 72.74 Mb| 220.25 byte| 2854| 356| 343447 (99.18%)| 343447 (99.18%)| 343447 (99.18%)| 343447 (99.18%)| 343447 (99.18%)| 274733 (79.33%)| 274733 (79.33%)| 343447 (99.18%)| 343447 (99.18%)| 343447 (99.18%)| 24900 (7.19%)
|whichbrowser/parser| 328153| 0.0003| 0.9174| 361.92| 0.0011| 96 byte| 10.2 Mb| 524.38 Mb| 1.64 Kb| 2948| 274| 320209 (97.58%)| 320209 (97.58%)| 325205 (99.1%)| 325205 (99.1%)| 325205 (99.1%)| 325205 (99.1%)| 325205 (99.1%)| 328153 (100%)| 325205 (99.1%)| 325205 (99.1%)| 32690 (9.96%)
|mimmi20/browser-detector| 346301| 0.0009| 1.3124| 1183.21| 0.0034| 96 byte| 4.79 Mb| 2.8 Gb| 8.48 Kb| 4057| 685| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 342244 (98.83%)| 15998 (4.62%)


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


[Database](https://drive.google.com/file/d/1OxqFXft5W_buBAhzLXJOrOy2rESUxHg6/view?usp=sharing) extract data.zip to runtime/*