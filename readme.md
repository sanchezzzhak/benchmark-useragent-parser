Info
---
* scoring when parsing a useragent, categories:
* For each definition 1 point is 🏆 awarded

##### Last date scan
2022/01/06
##### Total
| Parser Name | UA Count | Min time | Max time | Total time | Avg time | Min memory | Max memory | Total memory | Avg memory | Bots | Bot uniques | OS | OS versions | Client types | Client names | Client versions | Engine names | Engine versions | Device types | Brand names | Model names | Model unique names
| ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
|matomo/device-detector| 346301| 0| 0.5067| 1019.66| 0.0029| 96 byte| 6.24 Mb| 72.74 Mb| 220.25 byte| 2854| 356| 335595 (96.91%)| 305316 (88.16%)| 332954 (96.15%)| 332954 (96.15%)| 299041 (86.35%)| 267531 (77.25%)| 93662 (27.05%)| 318891 (92.08%)| 273851 (79.08%)| 259476 (74.93%)| 24900 (7.19%)
|whichbrowser/parser| 328153| 0.0003| 0.9174| 361.92| 0.0011| 96 byte| 10.2 Mb| 524.38 Mb| 1.64 Kb| 2948| 274| 301535 (91.89%)| 278230 (84.79%)| 263017 (80.15%)| 288155 (87.81%)| 215506 (65.67%)| 299925 (91.4%)| 91311 (27.83%)| 322858 (98.39%)| 237449 (72.36%)| 285449 (86.99%)| 32688 (9.96%)
|mimmi20/browser-detector| 346301| 0.0009| 1.2127| 1183.01| 0.0034| 96 byte| 4.79 Mb| 2.8 Gb| 8.48 Kb| 4057| 685| 340117 (98.21%)| 301464 (87.05%)| 338054 (97.62%)| 338054 (97.62%)| 324185 (93.61%)| 332446 (96%)| 315499 (91.11%)| 340514 (98.33%)| 274721 (79.33%)| 340514 (98.33%)| 15998 (4.62%)



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
| `php yii mobiledetectlib-parser` | analyze all useragents and save result to db |

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
* ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) [mobiledetect/mobiledetectlib](https://github.com/serbanghita/Mobile-Detect.git)


[Database](https://drive.google.com/file/d/1OxqFXft5W_buBAhzLXJOrOy2rESUxHg6/view?usp=sharing) extract data.zip to runtime/*