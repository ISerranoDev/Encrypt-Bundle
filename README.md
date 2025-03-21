# Encrypt Bundle
[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/ISerranoDev/Encrypt-Bundle/blob/main/README.en.md)
[![es](https://img.shields.io/badge/lang-es-yellow.svg)](https://github.com/ISerranoDev/Encrypt-Bundle/blob/main/README.md)

Este bundle proporciona funcionalidad de encriptación para entidades de Doctrine en aplicaciones Symfony.

## Instalación

1. Instala el bundle usando Composer:
```bash
composer require iserranodev/encrypt-bundle
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

Puedes personalizar la configuración en `config/packages/i_serrano_dev_encrypt.yaml`:

```yaml
i_serrano_dev_encrypt:
    encryption_key_path: '%kernel.project_dir%/encryption/encryption.key'  # Ruta por defecto
    hash_key: 'TuNuevaClaveHash'    # Opcional: sobreescribe ISD_ENCRYPT_HASH_KEY
    method: 'TuNuevoMetodo'         # Opcional: sobreescribe ISD_ENCRYPT_METHOD
    iv: 'TuNuevoIV'                 # Opcional: sobreescribe ISD_ENCRYPT_IV
```

## Uso

1. Genera la clave de encriptación (esto creará el archivo en la ruta configurada):
```bash
php bin/console iserranodev:encrypt-bundle:generate-key
```

2. Usa el atributo `#[Encrypted]` en las propiedades que desees encriptar:
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

3. Usa el atributo `#[Hashed]` en las propiedades que desees hashear y poder buscar en base de datos:
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

4. El bundle automáticamente:
    - Encriptará o aplicará un hash los datos antes de guardarlos en la base de datos
    - Desencriptará los datos cuando los recuperes
    - Manejará las migraciones de Doctrine correctamente
  
## Uso de EncryptService

Puedes usar dicho servicio para encriptar o hashear diferentes textos.
El atributo Encrypted usa los métodos hashData y unHashData para poder buscar en base de datos, pero si fuese necesario, también existe el método
encryptData y decryptData, el cual no es recomendable para el uso de campos que se pretenden buscar.

En caso de usar los métodos de encrypt, consultar la librería https://github.com/paragonie/halite, ya que el cifrado de los datos con estos métodos están desarrollados mediante dicha librería.

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
