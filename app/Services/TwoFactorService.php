<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    protected Google2FA $g2fa;

    public function __construct()
    {
        $this->g2fa = new Google2FA();
    }

    /**
     * Сгенерировать новый base32-секрет TOTP.
     */
    public function generate(): string
    {
        return $this->g2fa->generateSecretKey();
    }

    /**
     * Проверить 6-значный код по секрету (окно ±1 тайм-слот).
     */
    public function verify(string $code, string $secret): bool
    {
        return (bool) $this->g2fa->verifyKey($secret, $code);
    }

    /**
     * otpauth:// URL для кодирования в QR.
     */
    public function qrCodeUrl(string $secret, string $account): string
    {
        return $this->g2fa->getQRCodeUrl(config('app.name', 'Abrafin'), $account, $secret);
    }

    /**
     * SVG-марка QR-кода для otpauth URL.
     */
    public function qrCodeSvg(string $otpauthUrl, int $size = 300): string
    {
        $writer = new Writer(
            new ImageRenderer(new RendererStyle($size, 4), new SvgImageBackEnd())
        );

        return $writer->writeString($otpauthUrl);
    }
}
