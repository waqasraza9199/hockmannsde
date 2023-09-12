<?php declare(strict_types=1);

namespace RHWeb\ThemeFeatures\Core\Framework\Twig;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AnimatedExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('rhweb_animated', [$this, 'animated']),
            new TwigFunction('rhweb_random_bg', [$this, 'randomBg']),
            new TwigFunction('rhweb_element_animation', [$this, 'elementAnimation']),
            new TwigFunction('rhweb_animation', [$this, 'animation']),
            new TwigFunction('rhweb_block_behaviour', [$this, 'blockBehaviour']),
        ];
    }

    public function blockBehaviour(?array $behaviours = null, bool $isRow = false): ?string
    {
        if (!$behaviours) {
            return null;
        }

        $classes = [];

        try {
            foreach ($behaviours as $breakpoint => $behaviour) {
                if ($behaviour['inherit']) {
                    continue;
                }

                $classes[] = sprintf("d-%s-%s", $breakpoint, $behaviour['show'] ? 'block' : 'none');

                if (!$isRow) {
                    continue;
                }

                if (!empty($behaviour['width'])) {
                    $classes[] = sprintf("col-%s-%d", $breakpoint, $behaviour['width']);
                }

                if (!empty($behaviour['order'])) {
                    $classes[] = sprintf("order-%s-%d", $breakpoint, $behaviour['order']);
                }
            }

            if (empty($classes)) {
                return null;
            }

            return trim(str_replace("xs-", "", implode(" ", $classes)));
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function animated(array $config): string
    {
        $ar = [
            'animateIn' => 'data-animate-in',
            'animateOut' => 'data-animate-out',
            'animateHover' => 'data-animate-hover',
        ];
        $html = [];

        foreach ($ar as $ju => $das) {
            if (!empty($config[$ju]) && !empty($config[$ju]['value']) && $config[$ju]['value'] !== 'none') {
                $html[] = sprintf("%s=%s", $das, $config[$ju]['value']);
            }
        }

        return implode(" ", $html);
    }

    /**
     * @return string
     */
    public function randomBg(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public function elementAnimation(?CmsSlotEntity $element = null): ?string
    {
        if (!$element) {
            return null;
        }

        $config = $element->getFieldConfig();
        if (!$config->get('animateIn') && !$config->get('animateOut') && !$config->get('animateHover')) {
            return null;
        }
        if (!$config->get('animateIn')->getValue() && !$config->get('animateOut')->getValue() && !$config->get('animateHover')->getValue()) {
            return null;
        }
        if ($config->get('animateIn')->getValue() === 'none' && $config->get('animateOut')->getValue() === 'none' && $config->get('animateHover')->getValue() === 'none') {
            return null;
        }

        return json_encode([
            'animateIn' => [
                'type' => $config->get('animateIn') ? $config->get('animateIn')->getValue() : null,
                'speed' => $config->get('animateInSpeed') ? $config->get('animateInSpeed')->getValue() : 1000,
                'timeout' => $config->get('animateInTimeout') ? $config->get('animateInTimeout')->getValue() : 0,
                'rule' => $config->get('animateInRule') ? $config->get('animateInRule')->getValue() : 'isOverBottom'
            ],
            'animateOut' => [
                'type' => $config->get('animateOut') ? $config->get('animateOut')->getValue() : null,
                'speed' => $config->get('animateOutSpeed') ? $config->get('animateInSpeed')->getValue() : 1000,
                'timeout' => $config->get('animateOutTimeout') ? $config->get('animateOutTimeout')->getValue() : 0,
                'rule' => $config->get('animateOutRule') ? $config->get('animateOutRule')->getValue() : 'isInViewport'
            ],
            'animateHover' => [
                'type' => $config->get('animateHover') ? $config->get('animateHover')->getValue() : null,
                'speed' => $config->get('animateHoverSpeed') ? $config->get('animateHoverSpeed')->getValue() : 1000,
                'timeout' => $config->get('animateHoverTimeout') ? $config->get('animateHoverTimeout')->getValue() : 0,
                'rule' => $config->get('animateHoverRule') ? $config->get('animateHoverRule')->getValue() : 'isInViewport'
            ]
        ]);
    }

    public function animation(?array $elements = null): ?string
    {
        if (!$elements || empty($elements)) {
            return null;
        }

        $config = [];

        foreach ($elements as $element) {
            $config[] = [
                'cssSelector' => $element['cssSelector'],
                'animateIn' => [
                    'type' => $element['animateIn'],
                    'speed' => $element['animateInSpeed'],
                    'timeout' => $element['animateInTimeout'],
                    'rule' => $element['animateInRule']
                ],
                'animateOut' => [
                    'type' => $element['animateOut'],
                    'speed' => $element['animateOutSpeed'],
                    'timeout' => $element['animateOutTimeout'],
                    'rule' => $element['animateOutRule']
                ],
                'animateHover' => [
                    'type' => $element['animateHover'],
                    'speed' => $element['animateHoverSpeed'],
                    'timeout' => $element['animateHoverTimeout'],
                    'rule' => $element['animateHoverRule']
                ]
            ];
        }

        return json_encode($config);
    }
}
