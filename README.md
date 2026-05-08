# MYAPP Version 1.22

## SCREENSHOT 

### Dashboard
![Rasio Perkara](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/7.png?raw=true)
![Statistik](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/8.png?raw=true)
![Grafik](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/9.png?raw=true)
![Daftar Aplikasi](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/10.png?raw=true)

#### Notifikasi kinerja penyelesaian perkara (Bisa kirim notifikasi ke nomor Whatsapp atau Grup yang ditentukan)
![Notifikasi Kinerja Penyelesaian Perkara](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/6.jpeg?raw=true)

#### Notifikasi pengingat input BHT (Bisa kirim notifikasi ke nomor Whatsapp atau Grup yang ditentukan)
![Notifikasi Kontrol BHT](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/4.jpeg?raw=true)

#### Tampilan Menu Kontrol BHT
![Menu Kontrol BHT](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/1.png?raw=true)
![Menu Kontrol BHT](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/2.png?raw=true)
![Kirim Notifikasi BHT Secara Manual](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/3.png?raw=true)




## INSTALASI
1. Duplikasi file index.example.php dan rename file duplikat menjadi index.php
2. Masuk ke folder `application/config`
3. Duplikasi file `config.example.php` dan rename file duplikat menjadi `config.php`
4. Duplikasi file `cronjobs.example.php` dan rename file duplikat menjadi `cronjobs.php`
5. Duplikasi file `database.example.php` dan rename file duplikat menjadi `database.php`
6. Buka file `database.php` yang baru dibuat dan sesuaikan konfigurasi database (baris 4-12)
7. Pada terminal jalankan command berikut (SESUAIKAN PATH DAN NAMA FOLDER APLIKASI):
   ```bash
   chown apache:apache -R /var/www/html/myapp
   ```
8. Tes jalankan aplikasi.
9. Setelah aplikasi jalan dengan baik, hapus/comment baris 17-29 pada file `application/config/database.php`

## MODIFIKASI KONFIGURASI APLIKASI
Buka menu konfigurasi di:  
`http://[IP]/[NAMA FOLDER APLIKASI]/settings/config`

![alt text](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/5.png?raw=true)

## DEPLOY PRODUCTION
> [!CAUTION]
> STEP INI HANYA DILAKUKAN BILA:
> 1. Fungsi pengiriman notifikasi berhasil dilakukan
> 2. Notifikasi yang terkirim data dan teks sudah benar
> 3. Pengujian sistem notifikasi sudah dilakukan secara menyeluruh dan sesuai harapan
>
> Setelah aplikasi siap untuk digunakan LIVE, ubah environment ke production:
> 1. Buka file `index.php`
> 2. Pada baris 57, ubah:
>   ```php
>   define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
>   ```
>   menjadi:
>   ```php
>   define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');
>   ```

## OTOMATISASI NOTIFIKASI

Konfigurasi cron dapat dilakukan pada file `application/config/cronjobs.php`. Ubah sesuai preferensi Anda.

Contoh konfigurasi:
```php
$config['cronjobs'] = [
    [
        'expression' => '0 9 * * 1-5', // Senin–Jumat jam 09.00
        'command'    => 'php ' . FCPATH . 'index.php ck/bht send_notif_rencana_bht',
        'label'      => 'send_notif_rencana_bht',
    ],
    [
        'expression' => '0 16 * * 1-5', // Senin–Jumat jam 16.00
        'command'    => 'php ' . FCPATH . 'index.php site send_notif_kinerja',
        'label'      => 'send_notif_kinerja',
    ],
];
```

Setelah konfigurasi sesuai, buka tautan berikut di browser untuk membuat cron:  
`http://[IP]/[NAMA FOLDER APLIKASI]/cron`

Tautan ini cukup dijalankan satu kali setelah setiap perubahan konfigurasi.

## KONTAK
https://chandra.ct.ws/