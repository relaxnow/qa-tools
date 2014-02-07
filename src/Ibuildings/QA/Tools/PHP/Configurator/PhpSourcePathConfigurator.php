<?php

namespace Ibuildings\QA\Tools\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure PHP source paths
 *
 * Class PhpSourcePathConfigurator
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpSourcePathConfigurator
    implements ConfiguratorInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var DialogHelper
     */
    protected $dialog;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Settings $settings
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings
    )
    {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
    }

    public function configure()
    {
        if ($this->settings['enablePhpMessDetector']
            || $this->settings['enablePhpCodeSniffer']
            || $this->settings['enablePhpCopyPasteDetection']
        ) {
            $settings = $this->settings;
            $this->settings['phpSrcPath'] = $this->dialog->askAndValidate(
                $this->output,
                "What is the path to the PHP source code? [src] ",
                function ($data) use ($settings) {
                    if (is_dir($settings->getBaseDir() . '/' . $data)) {
                        return $data;
                    }
                    throw new \Exception("That path doesn't exist");
                },
                false,
                'src'
            );
        }
    }
}
