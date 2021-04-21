# lib-user-auth-bitbucket

Adalah module yang memungkinkan user login dengan social account bit bucket.
Sebagai catatan bahwa module ini tidak menangani proses auth pada sisi browser
client. Module ini hanya mengidentifikasi user berdasarkan login bitbucket.
Silahkan gunakan auth cookie untuk mempertahankan login user.

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-user-auth-bitbucket
```

## Konfigurasi

Tambahkan konfigurasi seperti di bawah pada aplikasi:

```php
return [
    'libUserAuthBitBucket' => [
        'client' => [
            'id' => '...',
            'secret' => '...'
        ]
    ]
];
```

## Penggunaan

Module ini menambahkan satu library dengan nama
`LibUserAuthBitBucket\Library\BitBucket` yang bisa digunakan untuk berkomunikasi
dengan bitbucket auth service.

```php
use LibUserAuthBitBucket\Library\BitBucket;

$error = BitBucket::getError();

// 1. Login Control::action
$auth_url = BitBucket::getAuthURL();
// Redirect user to $auth_url and wait for callback to continue below steps

// 2. Callback Control::action
$tokens = BitBucket::validateCode( $this->req->getQuery('code') );
$access_token = $tokens->access_token;
$refresh_token = $tokens->refresh_token;
$account = BitBucket::getAccount($access_token);
BitBucket::setUser($accounts->bitbucket->account_id, $this->user->id);

// 3. Other Control::action
$token = BitBucket::refreshToken($refresh_token);
```

## Methods

### function getAccount(string $token): ?object

Mengambil data akun bitbucket + lokal user berdasarkan access token yang didapat
dari step sebelumnya. Fungsi ini akan mengembalikan data sebagai berikut:

```
stdClass Object
(
    [bitbucket] => stdClass Object
        (
            [username] => bituser
            [has_2fa_enabled] =>
            [display_name] => BitBucket User
            [account_id] => 587068:01cb36cf-56a4-4f2d-ad9c-2d8174aa6bbf
            [links] => ...
            [nickname] => bitbucket-user
            [created_on] => 2015-02-28T18:32:09.786057+00:00
            [is_staff] =>
            [location] =>
            [account_status] => active
            [type] => user
            [uuid] => {d312d2d4-9e81-47d1-840c-cf2f3f5c12af}
        )

    [user] => stdClass Object
        (
            [id] => 1
            // other local user data
            [updated] => 2021-04-22 00:03:27
            [created] => 2021-04-22 00:03:27
        )

)
```

### static function getAuthURL(): string

Mengambil URL redirect yang digunakan untuk auth user bitbucket. Redirect user
ke URL ini untuk proses auth di sisi bitbucket.

### static function getError(): string

Mengambil pesan error terakhir.

### static function refreshToken(string $r_token): ?string

Mengambil `access_token` yang baru berdasarkan `refresh_token` yang ada.

### static function setUser($bitbucket, $user): void

Menset user lokal dengan akun bitbucket. Pastikan memanggil fungsi ini pada saat
pertama kali user bitbucket bersangkutan menggunakan auth pada aplikasi.

### static function validateCode(string $code): ?object

Mengambil access_token dari callback auth url. Fungsi ini akan mengembalikan data
seperti:

```
stdClass Object
(
    [scopes] => webhook account repository
    [access_token] => ...
    [expires_in] => 7200
    [token_type] => bearer
    [state] => authorization_code
    [refresh_token] => 4qeyxZTXQqTScN59CaA
)
```
