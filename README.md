#  LARAVEL - ACR FİLE -- FİLE UPLOAD CLASS

[Query-File-Upload](https://github.com/blueimp/jQuery-File-Upload): Paketi refarans alarak oluşturulmuştur.

## Kurulum:
####composer json : 
```
"acr/file": "dev-file"
```
### CONFİG

#### Providers
```
Acr\File\AcrFileServiceProviders::class
```
#### Aliases
```
'AcrFile'      => Acr\File\Facades\AcrFile::class
```
### acr_file_id

```php 
PHP
$acr_file_id = AcrFile::create($acr_file_id); 
```
acr_file_id: ilişkili tablodan gelmeli örneğin ürünler için kullanacaksanız urun tablonuzda acr_file_id stunu olmalı, acr_file_id değişkeni null gelirse : $acr_file_id = AcrFile::create($acr_file_id) yeni bir acr_file_id oluşturur.
```php 
PHP
 echo AcrFile::css();  
```
CSS dosyalarını yükler.
```php 
PHP
echo AcrFile::form()
```
Formu yükler
```php 
PHP
AcrFile::js($acr_file_id)
```
Java script dosylarını yükler.

```sql 
Mysql Tablosu
CREATE TABLE `acr_files` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(66) COLLATE utf8_turkish_ci DEFAULT NULL,
  `file_dir` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sil` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;
```
Dosya yolu  /acr_files/{session_id}
