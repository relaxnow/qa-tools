<?php

namespace Ibuildings\QA\Tools\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHPLint
 *
 * Class PhpLintConfigurator
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpLintConfigurator
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

        $this->settings['enablePhpLint'] = false;
    }

    /**
     *
     */
    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

        $this->settings['enablePhpLint'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable PHP Lint? [Y/n] ",
            true
        );
    }
}
