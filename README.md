# Encrypt Bundle

Este bundle proporciona funcionalidad de encriptación para entidades de Doctrine en aplicaciones Symfony.

## Instalación

1. Instala el bundle usando Composer:
```bash
composer require iserrano-dev/encrypt-bundle
```

2. Habilita el bundle en `config/bundles.php`:
```php
return [
    // ...
    ISerranoDev\EncryptBundle\ISerranoDevEncryptBundle::class => ['all' => true],
];
```

## Configuración

### Variables de Entorno

El bundle utiliza las siguientes variables que puedes configurar en tu archivo `.env`:

```env
# Valores por defecto proporcionados
ISD_ENCRYPT_HASH_KEY=UsLN^Dc6x9xP7n924NJoffw4$6p*9SNg#r0Qql#^bNusXh4dKU
ISD_ENCRYPT_METHOD=AES-128-CBC
ISD_ENCRYPT_IV=5414358938341622
```

### Configuración del Bundle

Puedes personalizar la configuración en `config/packages/isd_encrypt.yaml`:

```yaml
isd_encrypt:
    encryption_key_path: '%kernel.project_dir%/encryption/encryption.key'  # Ruta por defecto
    hash_key: 'TuNuevaClaveHash'    # Opcional: sobreescribe ISD_ENCRYPT_HASH_KEY
    method: 'TuNuevoMetodo'         # Opcional: sobreescribe ISD_ENCRYPT_METHOD
    iv: 'TuNuevoIV'                 # Opcional: sobreescribe ISD_ENCRYPT_IV
```

## Uso

1. Genera la clave de encriptación (esto creará el archivo en la ruta configurada):
```bash
php bin/console app:generate-key
```

2. Usa el atributo `#[Encrypted]` en las propiedades que desees encriptar:
```php
use ISerranoDev\EncryptBundle\Attribute\Encrypted;

class User
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Encrypted]
    private ?string $sensitiveData = null;
}
```

3. El bundle automáticamente:
    - Encriptará los datos antes de guardarlos en la base de datos
    - Desencriptará los datos cuando los recuperes
    - Manejará las migraciones de Doctrine correctamente

## Ubicación de la Clave de Encriptación

Por defecto, el archivo de clave se guarda en:
```
tu-proyecto/
├── encryption/
│   └── encryption.key
```

Puedes cambiar esta ubicación en la configuración del bundle.

## Seguridad

- No subas el archivo de clave (`encryption.key`) a tu repositorio
- Asegúrate de incluir `encryption/` en tu `.gitignore`
- Mantén una copia segura de tu clave de encriptación
- Considera usar variables de entorno en producción

## Soporte para Migraciones

El bundle incluye soporte para migraciones de Doctrine. Para usarlo en tus migraciones:

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
        // Usa $this->encryptService para encriptar/desencriptar datos
    }
}
```
