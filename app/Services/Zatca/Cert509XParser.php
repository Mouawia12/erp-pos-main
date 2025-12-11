<?php
namespace App\Services\Zatca;

use phpseclib3\Crypt\Common\PublicKey as PhpseclibPublicKey;
use phpseclib3\File\X509;
use RuntimeException;

/**
 * A class defines certificate parser
 */
class Cert509XParser
{
    private string $certificateEncoded;
    private string $certificateSecret;
    private string $privateKey;
    private X509 $x509;
    private ?string $normalizedCertificateBody = null;

    public function __construct()
    {
        $this->x509 = new X509();
    }

    /**
     * Set certificate encoded
     *
     * @param string $certificateEncoded
     *
     * @return $this
     */
    public function setCertificateEncoded($certificateEncoded): self
    {
        $this->certificateEncoded = $certificateEncoded;

        return $this;
    }

    /**
     * Set certificate secret
     *
     * @param string $certificateSecret
     *
     * @return $this
     */
    public function setCertificateSecret($certificateSecret): self
    {
        $this->certificateSecret = $certificateSecret;

        return $this;
    }

    /**
     * Set private key
     *
     * @param string $privateKey
     *
     * @return $this
     */
    public function setPrivateKeyEncoded($privateKey): self
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Get certificate decoded value
     *
     * @return string
     */
    public function getCertificateDecoded(): string
    {
        if ($this->normalizedCertificateBody === null) {
            $decoded = base64_decode($this->certificateEncoded, true);
            if ($decoded === false) {
                throw new RuntimeException('Invalid ZATCA certificate encoding.');
            }

            $decoded = trim($decoded);

            if (str_contains($decoded, 'BEGIN CERTIFICATE')) {
                $decoded = $this->stripPemSection($decoded, 'CERTIFICATE');
            } elseif ($this->containsBinaryData($decoded)) {
                $decoded = chunk_split(base64_encode($decoded), 64, "\r\n");
            }

            $this->normalizedCertificateBody = preg_replace('/\s+/', '', $decoded);
        }

        return $this->normalizedCertificateBody;
    }

    /**
     * Get private key decoded
     *
     * @return string
     */
    public function getPrivateKeyDecoded(): string
    {
        return base64_decode($this->privateKey);
    }

    /**
     * Get certificate with headers and footers
     *
     * @return string
     */
    public function getCertificate(): string
    {
        $body = chunk_split($this->getCertificateDecoded(), 64, "\r\n");

        return "-----BEGIN CERTIFICATE-----\r\n" . $body . "-----END CERTIFICATE-----";
    }

    /**
     * Get certificate hash base64 encoded
     *
     * @return string
     */
    public function getCertificateHashEncoded(): string
    {
        return base64_encode(hash('sha256', $this->getCertificateDer(), true));
    }

    /**
     * Get certificate signature
     *
     * @return string
     */
    public function getCertificateSignature(): string
    {
        $certOut = $this->ensureCertificateParsed();
        $signature = unpack('H*', $certOut['signature'])['1'];

        return pack('H*', substr($signature, 2));
    }

    /**
     * Get certificate public key base64 encoded
     *
     * @return string
     */
    public function getCertificatePublicKeyEncoded(): string
    {
        $this->ensureCertificateParsed();
        $publicKey = $this->x509->getPublicKey();
        if ($publicKey instanceof PhpseclibPublicKey) {
            $publicKey = $publicKey->toString('PKCS8');
        }
        if (! is_string($publicKey) || trim($publicKey) === '') {
            throw new RuntimeException('Unable to extract ZATCA certificate public key. Verify the certificate configuration.');
        }
        $publicKey = str_replace('-----BEGIN PUBLIC KEY-----', '', $publicKey);
        $publicKey = str_replace('-----END PUBLIC KEY-----', '', $publicKey);

        return base64_decode($publicKey);
    }

    /**
     * Get certificate serial number
     *
     * @return string
     */
    public function getCertificateSerialNumber(): string
    {
        $certOut = $this->ensureCertificateParsed();

        if (! isset($certOut['tbsCertificate']['serialNumber'])) {
            throw new RuntimeException('Unable to read ZATCA certificate serial number. Verify the certificate configuration.');
        }

        return $certOut['tbsCertificate']['serialNumber']->toString();
    }

    /**
     * Get certificate issuer name
     *
     * @return string
     */
    public function getCertificateIssuerName(): string
    {
        $this->ensureCertificateParsed();
        $issuer_names = [];
        $issuer_info = $this->x509->getIssuerDN(X509::DN_OPENSSL);
        if (! is_array($issuer_info)) {
            throw new RuntimeException('Unable to parse ZATCA certificate issuer. Verify the certificate configuration.');
        }

        foreach ($issuer_info as $key_parent => $string_row) {
            if ($key_parent == '0.9.2342.19200300.100.1.25') {
                foreach ($string_row as $string) {
                    $issuer_names[] = 'DC=' . $string;
                }
            }
            if ($key_parent == 'CN') {
                $issuer_names[] = 'CN=' . $string_row;
            }
        }

        return implode(', ', array_reverse($issuer_names));
    }

    protected function ensureCertificateParsed(): array
    {
        $certOut = $this->x509->loadX509($this->getCertificate());
        if (! is_array($certOut)) {
            throw new RuntimeException('Unable to parse ZATCA certificate. Verify the certificate configuration.');
        }

        return $certOut;
    }

    protected function getCertificateDer(): string
    {
        $body = preg_replace('/\s+/', '', $this->getCertificateDecoded());
        $binary = base64_decode($body, true);
        if ($binary === false) {
            throw new RuntimeException('Unable to decode ZATCA certificate body.');
        }

        return $binary;
    }

    protected function containsBinaryData(string $value): bool
    {
        return preg_match('/[^\x20-\x7E\r\n]/', $value) === 1;
    }

    protected function stripPemSection(string $pem, string $label): string
    {
        $pem = str_replace("-----BEGIN {$label}-----", '', $pem);
        $pem = str_replace("-----END {$label}-----", '', $pem);

        return trim(preg_replace('/\s+/', '', $pem));
    }
}
