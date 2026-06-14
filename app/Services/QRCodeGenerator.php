<?php

namespace App\Services;

use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Common\EccLevel;

class QRCodeGenerator
{
    public function generate(
        string $data,
        int $size,
        string $finderColor = '#000000',
        string $finderInnerColor = '#000000',
        string $dataColor = '#000000',
        string $bgColor = '#FFFFFF',
        ?string $logoPath = null
    ): string {
        $finderRgb = $this->hexToRgb($finderColor);
        $finderInnerRgb = $this->hexToRgb($finderInnerColor);
        $dataRgb = $this->hexToRgb($dataColor);
        $bgRgb = $this->hexToRgb($bgColor);

        $scale = max(10, (int) ($size / 25));

        $config = [
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => false,
            'scale' => $scale,
            'eccLevel' => EccLevel::H,
            'bgColor' => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
            'moduleValues' => [
                QRMatrix::M_DATA => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_FINDER => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_SEPARATOR => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_ALIGNMENT => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_TIMING => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_FORMAT => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_VERSION => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_QUIETZONE => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_LOGO => [$bgRgb['r'], $bgRgb['g'], $bgRgb['b']],
                QRMatrix::M_FINDER_DARK => [$finderRgb['r'], $finderRgb['g'], $finderRgb['b']],
                QRMatrix::M_FINDER_DOT => [$finderInnerRgb['r'], $finderInnerRgb['g'], $finderInnerRgb['b']],
                QRMatrix::M_DATA_DARK => [$dataRgb['r'], $dataRgb['g'], $dataRgb['b']],
                QRMatrix::M_ALIGNMENT_DARK => [$dataRgb['r'], $dataRgb['g'], $dataRgb['b']],
                QRMatrix::M_TIMING_DARK => [$dataRgb['r'], $dataRgb['g'], $dataRgb['b']],
                QRMatrix::M_FORMAT_DARK => [$dataRgb['r'], $dataRgb['g'], $dataRgb['b']],
                QRMatrix::M_VERSION_DARK => [$dataRgb['r'], $dataRgb['g'], $dataRgb['b']],
                QRMatrix::M_DARKMODULE => [$dataRgb['r'], $dataRgb['g'], $dataRgb['b']],
            ],
        ];

        $options = new QROptions($config);

        $qrcode = new QRCode($options);
        $qrcode->addByteSegment($data);
        $matrix = $qrcode->getQRMatrix();

        if ($logoPath) {
            $moduleCount = $matrix->getSize();
            $logoSpaceSize = (int) max(4, min(10, ceil($moduleCount * 0.15)));
            $matrix->setLogoSpace($logoSpaceSize, $logoSpaceSize);
        }

        $png = $qrcode->renderMatrix($matrix);

        if ($logoPath && file_exists($logoPath)) {
            $logoSpaceRatio = isset($logoSpaceSize) ? ($logoSpaceSize / $matrix->getSize()) : 0.20;
            $png = $this->overlayLogo($png, $logoPath, $size, $logoSpaceRatio);
        }

        return $png;
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            'r' => (int) hexdec(substr($hex, 0, 2)),
            'g' => (int) hexdec(substr($hex, 2, 2)),
            'b' => (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    private function overlayLogo(string $qrCodeImage, string $logoPath, int $targetSize, float $logoSpaceRatio): string
    {
        $qr = imagecreatefromstring($qrCodeImage);
        $logo = imagecreatefromstring(file_get_contents($logoPath));

        if (! $qr || ! $logo) {
            return $qrCodeImage;
        }

        $length = imagesx($qr);
        $logoWidth = imagesx($logo);
        $logoHeight = imagesy($logo);

        $maxLogoSize = (int) ($length * $logoSpaceRatio * 0.9);
        $ratio = min($maxLogoSize / $logoWidth, $maxLogoSize / $logoHeight);
        $newWidth = (int) ($logoWidth * $ratio);
        $newHeight = (int) ($logoHeight * $ratio);

        $resizedLogo = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resizedLogo, false);
        imagesavealpha($resizedLogo, true);
        imagecopyresampled($resizedLogo, $logo, 0, 0, 0, 0, $newWidth, $newHeight, $logoWidth, $logoHeight);

        $x = (int) (($length - $newWidth) / 2);
        $y = (int) (($length - $newHeight) / 2);

        imagealphablending($qr, true);
        imagesavealpha($qr, true);
        imagecopy($qr, $resizedLogo, $x, $y, 0, 0, $newWidth, $newHeight);

        if ($length !== $targetSize) {
            $qr = imagescale($qr, $targetSize, $targetSize, IMG_BICUBIC_FIXED);
        }

        ob_start();
        imagepng($qr);
        $result = ob_get_clean();

        imagedestroy($qr);
        imagedestroy($logo);
        imagedestroy($resizedLogo);

        return $result;
    }
}
