# Laravel 9.x - Webudi RESTful API

## Kurulum

### 1. Projeyi klonlayın

```bash
git clone https://github.com/erhanurgun/TSK02-laravel-webudi-restful-api.git
```

### 2. .env dosyasını oluşturun

```bash
cp .env.example .env
```

### 3. .env dosyasını düzenleyin

```conf
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="demo@urgun.com.tr"

# https://laravel.com/docs/9.x/passport#creating-a-personal-access-client
PASSPORT_PERSONAL_ACCESS_CLIENT_ID="client-id-value"
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="unhashed-client-secret-value"

SPOTIFY_SCOPES="user-read-private user-read-email"
SPOTIFY_CLIENT_ID="client-id-value"
SPOTIFY_USER_NAME="user-name-value"
SPOTIFY_PLAYLIST_ID="playlist-id-value"
SPOTIFY_CLIENT_SECRET="unhashed-client-secret-value"
```


### 4. Composer paketlerini yükleyin

```bash
composer install
```

### 5. NPM paketlerini yükleyin

```bash
npm install
```

### 6. Key oluşturun

```bash
php artisan key:generate
```

### 6. Veritabanı eklemelerini yapın

```bash
php artisan migrate
```

### 7. Veritabanı seed eklemelerini yapın ( isteğe bağlı )

```bash
php artisan mifrate:fresh --seed
```

## Kullanım

### 1. Aşağıdaki komutları çalıştırınız

```bash
php artisan serve
```

```bash
npm run watch
```

### 2. Tarayıcıdan aşağıdaki adrese gidiniz

```bash
http://localhost:8000/admin
```

### 2. Bu bilgilerle admin paneline giriş yapıp kullanabilirsiniz

```conf
E-Posta: erhan@urgun.com.tr
Şifre..: Demo123*
