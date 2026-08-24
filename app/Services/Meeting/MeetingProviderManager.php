<?php

namespace App\Services\Meeting;

use App\Services\Meeting\Contracts\MeetingProviderInterface;
use App\Services\Meeting\Providers\CustomEmbeddedProvider;
use App\Services\Meeting\Providers\GoogleMeetProvider;
use App\Services\Meeting\Providers\MicrosoftTeamsProvider;
use App\Services\Meeting\Providers\ZoomMeetingProvider;
use InvalidArgumentException;

class MeetingProviderManager
{
    /**
     * @var array<string, MeetingProviderInterface>
     */
    protected array $providers = [];

    public function __construct()
    {
        $this->registerProvider(new ZoomMeetingProvider());
        $this->registerProvider(new GoogleMeetProvider());
        $this->registerProvider(new MicrosoftTeamsProvider());
        $this->registerProvider(new CustomEmbeddedProvider());
    }

    public function registerProvider(MeetingProviderInterface $provider): void
    {
        $this->providers[$provider->getSlug()] = $provider;
    }

    public function resolve(string $slug): MeetingProviderInterface
    {
        if (isset($this->providers[$slug])) {
            return $this->providers[$slug];
        }

        // Fallback mapping for common aliases
        $normalized = strtolower(str_replace('-', '_', $slug));
        if (isset($this->providers[$normalized])) {
            return $this->providers[$normalized];
        }

        // Default to Google Meet if unknown
        return $this->providers['google_meet'] ?? new GoogleMeetProvider();
    }

    public function getRegisteredProviders(): array
    {
        return $this->providers;
    }
}
