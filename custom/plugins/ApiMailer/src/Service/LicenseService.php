<?php

declare(strict_types=1);

namespace LZYT8\ApiMailer\Service;

use DateTime;
use GuzzleHttp\Client;
use Shopware\Core\Framework\Context;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemInterface;
use LZYT8\ApiMailer\Service\ConfigurationService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class LicenseService {

    private $client;
    private EntityRepository $salesChannelRepository;
    private ConfigurationService $configurationService;
    private FilesystemInterface $fileSystemPrivate;

    const CHECKSUM = 'b8130494ea670009857b841f101cd386';

    public function __construct(
        EntityRepository $salesChannelRepository, 
        ConfigurationService $configurationService, 
        FilesystemInterface $fileSystemPrivate
    )
    {
        $this->client = new Client();
        $this->configurationService = $configurationService;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->fileSystemPrivate = $fileSystemPrivate;
    }

    public function isValid(string $salesChannelId) {
        if (!$this->hasToCheckLicense())
            return true;

        try {
            $response = $this->client->get('https://www.settings-manager.xyz/api/License/CheckLicenseKey?licenseKey=' . $this->configurationService->getLicenseKey() . '&hwid=' . $this->getCurrentDomain($salesChannelId) . '&checksum=' . self::CHECKSUM);
            $response = json_decode((string) $response->getBody(), true);

            if ($response['licenseIsActive'] && $response['hwidIsValid']) {
                $this->writePrivateFile('license', base64_encode((new DateTime())->modify('+6 hours')->format('Y-m-d H:i:s')));
                return true;
            }
        }catch(\Exception $e) {
            return false;
        }

        return false;
    }

    public function fetchValidUntil() : ?DateTime {
        $cachedExpiryDate = $this->fetchCachedValidUntil();
        if ($cachedExpiryDate != null)
            return $cachedExpiryDate;

        try {
            $response = $this->client->get('https://www.settings-manager.xyz/api/License/GetKeyExpiryDate?licenseKey=' . $this->configurationService->getLicenseKey());
            $response = json_decode((string) $response->getBody(), true);

            if ($response['status'] == 400)
                return null;

            $expiryDate = new DateTime($response['expiryDate']);
            $this->writePrivateFile('license-cached', base64_encode(json_encode(['cachedUntil' => (new DateTime())->modify('+1 hours')->format('Y-m-d H:i:s'), 'validUntil' => $expiryDate->format('Y-m-d H:i:s')])));
            return $expiryDate ;
        }catch(\Exception $e) {
            var_dump($e->getMessage());die();
        }
    }

    private function getCurrentDomain(string $salesChannelId) : string {
        $criteria = new Criteria([$salesChannelId]);
        $criteria->addAssociation('domains');
        $salesChannel = $this->salesChannelRepository->search($criteria, Context::createDefaultContext())->first();

        return parse_url($salesChannel->getDomains()->first()->getUrl(), \PHP_URL_HOST);
    }

    private function hasToCheckLicense(): bool {
        $content = $this->readPrivateFile('license');
        if (empty($content))
            return true;

        $value = base64_decode($content);
        $dateTime = DateTime::createFromFormat("Y-m-d H:i:s", $value);
        return new DateTime() > $dateTime;
    }

    private function fetchCachedValidUntil() : ?DateTime {
        $content = $this->readPrivateFile('license-cached');
        if (empty($content))
            return null;

        $data = json_decode(base64_decode($content), true);
        $cachedTime = DateTime::createFromFormat("Y-m-d H:i:s", $data['cachedUntil']);
        if (new DateTime() > $cachedTime)
            return null;

        return DateTime::createFromFormat("Y-m-d H:i:s", $data['validUntil']);
    }   

    private function readPrivateFile(string $filename) : ?string {
        try {
            return $this->fileSystemPrivate->read($filename);
        } catch (FilesystemException | UnableToReadFile $exception) {
            return null;
        }
    }

    private function writePrivateFile(string $filename, string $content) : void {
        $this->fileSystemPrivate->put($filename, $content);
    }
}