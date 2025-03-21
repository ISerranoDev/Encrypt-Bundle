# Encrypt Bundle
[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/ISerranoDev/Encrypt-Bundle/blob/main/README.en.md)
[![es](https://img.shields.io/badge/lang-es-yellow.svg)](https://github.com/ISerranoDev/Encrypt-Bundle/blob/main/README.md)

This bundle provides encryption functionality for Doctrine entities in Symfony applications.

## Installation

1. Install the bundle using Composer:
```bash
composer require iserranodev/encrypt-bundle
```

2. Enable the bundle in `config/bundles.php`:
```php
return [
    // ...
    ISerranoDev\EncryptBundle\ISerranoDevEncryptBundle::class => ['all' => true],
];
```

## Configuration

### Environment Variables

The bundle uses the following variables, which you can configure in your `.env` file:

```env
# Default provided values
ISD_ENCRYPT_HASH_KEY=UsLN^Dc6x9xP7n924NJoffw4$6p*9SNg#r0Qql#^bNusXh4dKU
ISD_ENCRYPT_METHOD=AES-128-CBC
ISD_ENCRYPT_IV=5414358938341622
```

### Bundle Configuration

You can customize the configuration in `config/packages/i_serrano_dev_encrypt.yaml`:

```yaml
i_serrano_dev_encrypt:
    encryption_key_path: '%kernel.project_dir%/encryption/encryption.key'  # Default path
    hash_key: 'YourNewHashKey'    # Optional: overrides ISD_ENCRYPT_HASH_KEY
    method: 'YourNewMethod'       # Optional: overrides ISD_ENCRYPT_METHOD
    iv: 'YourNewIV'               # Optional: overrides ISD_ENCRYPT_IV
```

## Usage

1. Generate the encryption key (this will create the file at the configured path):
```bash
php bin/console iserranodev:encrypt-bundle:generate-key
```

2. Use the `#[Encrypted]` attribute on properties you want to encrypt:
```php
use ISerranoDev\EncryptBundle\Attribute\Encrypted;
use App\EventListener\EncryptListener;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'users')]
#[ORM\EntityListeners([EncryptListener::class])]
class User
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Encrypted]
    private ?string $sensitiveData = null;
}
```

3. Use the `#[Hashed]` attribute on properties you want to hash and search in the database:
```php
use ISerranoDev\EncryptBundle\Attribute\Hashed;
use App\EventListener\HashListener;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'users')]
#[ORM\EntityListeners([HashListener::class])]
class User
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Hashed]
    private ?string $sensitiveData = null;
}
```

3. The bundle automatically:
   - Encrypts or hash data before storing it in the database
   - Decrypts data when retrieving it
   - Handles Doctrine migrations correctly

## Using EncryptService

You can use this service to encrypt or hash different texts.
The Encrypted attribute uses the hashData and unHashData methods to search the database, but if necessary, there is also the
encryptData and decryptData methods, which are not recommended for use with fields that are intended to be searched.

If you use encrypt methods, consult the library https://github.com/paragonie/halite, since data encryption with these methods is developed using said library.

## Encryption Key Location

By default, the key file is stored in:
```
your-project/
├── encryption/
│   └── encryption.key
```

You can change this location in the bundle configuration.

## Security

- Do not upload the key file (`encryption.key`) to your repository.
- Ensure `encryption/` is included in your `.gitignore` file.
- Keep a secure backup of your encryption key.
- Consider using environment variables in production.

## Migration Support

The bundle includes support for Doctrine migrations. To use it in your migrations:

```php
use ISerranoDev\EncryptBundle\Interface\EncryptAwareMigrationInterface;

final class Version20240214123456 extends AbstractMigration implements EncryptAwareMigrationInterface
{
    private EncryptService $encryptService;

    public function setEncryptService(EncryptService $encryptService): void
    {
        $this->encryptService = $encryptService;
    }

    public function up(Schema $schema): void
    {
        // Use $this->encryptService to encrypt/decrypt data
    }
}
```

