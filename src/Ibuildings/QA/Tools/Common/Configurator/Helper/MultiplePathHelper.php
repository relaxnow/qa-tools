<?php
namespace Ibuildings\QA\Tools\Common\Configurator\Helper;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Helps to set multiple paths
 *
 * Class MultiplePathHelper
 *
 * @package Ibuildings\QA\Tools\PHP\Configurator\Helper
 */
class MultiplePathHelper
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
     * @var string
     */
    protected $baseDir;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param string $baseDir
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        $baseDir
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->baseDir = $baseDir;
    }

    /**
     * Convenience wrapper for this->ask()
     *
     * @param string $pathQuestion
     * @param string $defaultPaths
     * @param null $confirmationQuestion Optional question to ask if you want to set the value
     * @param bool $defaultConfirmation
     *
     * @return string
     */
    public function askPatterns(
        $pathQuestion,
        $defaultPaths,
        $confirmationQuestion = null,
        $defaultConfirmation = true
    ) {

        $callback = function ($data) {
          $paths = explode(',', $data);

          $trimmedPaths = array();
          foreach ($paths as $path) {
            $trimmedPaths[] = trim($path);
          }

          return $trimmedPaths;
        };

      return $this->ask(
        $pathQuestion,
        $defaultPaths,
        $confirmationQuestion,
        $defaultConfirmation,
        $callback
      );
    }

    /**
     * Convenience wrapper for this->ask()
     *
     * @param string $pathQuestion
     * @param string $defaultPaths
     * @param null $confirmationQuestion Optional question to ask if you want to set the value
     * @param bool $defaultConfirmation
     *
     * @return string
     */
    public function askPaths(
        $pathQuestion,
        $defaultPaths,
        $confirmationQuestion = null,
        $defaultConfirmation = true
    ) {

      $baseDir = $this->baseDir;
      $callback = function ($data) use ($baseDir) {
        $paths = explode(',', $data);
        $trimmedPaths = array();

        foreach ($paths as $path) {
          $trimmedPath = trim($path);

          // Check paths
          $fullPath = $baseDir . DIRECTORY_SEPARATOR . $trimmedPath;
          if (!is_dir($fullPath)) {
            throw new \Exception("path '{$fullPath}' doesn't exist");
          }

          $trimmedPaths[] = $trimmedPath;
        }

        return $trimmedPaths;
      };

      return $this->ask(
        $pathQuestion,
        $defaultPaths,
        $confirmationQuestion,
        $defaultConfirmation,
        $callback
      );
    }

    /**
     * Ask the user for one or more paths/patterns
     *
     * @param string $pathQuestion
     * @param string $defaultPaths
     * @param null $confirmationQuestion Optional question to ask if you want to set the value
     * @param bool $defaultConfirmation
     * @param callable $callback
     *
     * @return string
     */
    protected function ask(
        $pathQuestion,
        $defaultPaths,
        $confirmationQuestion,
        $defaultConfirmation,
        $callback
    ) {

        /**
         * Type hinting with callable (@see http://www.php.net/manual/en/language.types.callable.php)
         * is only from PHP5.4+ and therefore we check with is_callable()
         */
        if (!is_callable($callback)) {
            throw new \Exception('Error calling callable');
        }

        if ($defaultPaths) {
            $pathQuestion .= " [$defaultPaths]";
        }

        $defaultConfirmationText = ' [Y/n] ';
        if ($defaultConfirmation === false) {
            $defaultConfirmationText = ' [y/N] ';
        }

        if ($confirmationQuestion) {
            if (!$this->dialog->askConfirmation(
                $this->output,
                $confirmationQuestion . $defaultConfirmationText,
                $defaultConfirmation
            )
            ) {
                return array();
            }
        }

        return $this->dialog->askAndValidate(
            $this->output,
            $pathQuestion . ' (comma separated)',
            $callback,
            false,
            $defaultPaths
        );
    }
}
