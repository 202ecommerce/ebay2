<?php

/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace EbayVendor\PrestaShop\HeaderStamp\Command;

use EbayVendor\PhpParser\ParserFactory;
use EbayVendor\PrestaShop\HeaderStamp\LicenseHeader;
use EbayVendor\PrestaShop\HeaderStamp\Reporter;
use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Helper\ProgressBar;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Input\InputOption;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
use EbayVendor\Symfony\Component\Console\Style\SymfonyStyle;
use EbayVendor\Symfony\Component\Finder\Finder;
use EbayVendor\Symfony\Component\Finder\SplFileInfo;
class UpdateLicensesCommand extends Command
{
    const DEFAULT_LICENSE_FILE = __DIR__ . '/../../assets/osl3.txt';
    const DEFAULT_EXTENSIONS = ['php', 'js', 'css', 'scss', 'tpl', 'html.twig', 'json', 'vue'];
    const DEFAULT_FILTERS = [];
    /**
     * License content
     *
     * @param string $text
     */
    private $text;
    /**
     * License file path (not content)
     *
     * @param string $license
     */
    private $license;
    /**
     * @var string
     */
    private $targetDirectory;
    /**
     * List of extensions to update
     *
     * @param array $extensions
     */
    private $extensions;
    /**
     * List of folders and files to exclude from the search
     *
     * @param array $filters
     */
    private $filters;
    /**
     * dry-run feature flag
     *
     * @var bool
     */
    private $runAsDry;
    /**
     * display-report feature flag
     *
     * @var bool
     */
    private $displayReport;
    /**
     * Reporter in charge of monitoring what is done and provide a complete report
     * at the end of execution
     *
     * @var Reporter
     */
    private $reporter;
    /**
     * @var string
     */
    private $discriminationString;
    protected function configure()
    {
        $this->setName('prestashop:licenses:update')->setDescription('Rewrite your file headers to add the license or to make them up-to-date')->addOption('license', null, InputOption::VALUE_REQUIRED, 'License file to apply', \realpath(static::DEFAULT_LICENSE_FILE))->addOption('target', null, InputOption::VALUE_REQUIRED, 'Folder to work in (default: current dir)')->addOption('exclude', null, InputOption::VALUE_REQUIRED, 'Comma-separated list of folders and files to exclude from the update', \implode(',', static::DEFAULT_FILTERS))->addOption('extensions', null, InputOption::VALUE_REQUIRED, 'Comma-separated list of file extensions to update', \implode(',', static::DEFAULT_EXTENSIONS))->addOption('display-report', null, InputOption::VALUE_NONE, 'Whether or not to display a report')->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry-run mode does not modify files')->addOption('header-discrimination-string', null, InputOption::VALUE_OPTIONAL, 'Fix existing licenses only if they contain that string', 'prestashop');
    }
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->extensions = \explode(',', $input->getOption('extensions'));
        $this->filters = \explode(',', $input->getOption('exclude'));
        $this->license = $input->getOption('license');
        if ($input->getOption('target')) {
            $this->targetDirectory = \realpath($input->getOption('target'));
        } else {
            $this->targetDirectory = \getcwd();
        }
        $this->runAsDry = $input->getOption('dry-run') === \true;
        $this->displayReport = $input->getOption('display-report') === \true;
        $this->discriminationString = $input->getOption('header-discrimination-string');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->text = \trim((new LicenseHeader($this->license))->getContent(), \PHP_EOL);
        $this->reporter = new Reporter();
        foreach ($this->extensions as $extension) {
            $this->findAndCheckExtension($input, $output, $extension);
        }
        if ($this->runAsDry) {
            $this->printDryRunPrettyReport($input, $output);
            if (empty($this->reporter->getReport()['fixed'])) {
                return 0;
            }
            return 1;
        }
        if ($this->displayReport) {
            $this->printPrettyReport($input, $output);
        }
        return 0;
    }
    private function findAndCheckExtension(InputInterface $input, OutputInterface $output, $ext)
    {
        if ($this->targetDirectory === \false) {
            throw new \Exception('Could not get target directory. Check your permissions.');
        }
        $finder = new Finder();
        $finder->files()->name('*.' . $ext)->in($this->targetDirectory)->exclude($this->filters);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $output->writeln('Updating license in ' . \strtoupper($ext) . ' files ...');
        $progress = new ProgressBar($output, \count($finder));
        $progress->start();
        $progress->setRedrawFrequency(20);
        foreach ($finder as $file) {
            switch ($file->getExtension()) {
                case 'php':
                    try {
                        $nodes = $parser->parse($file->getContents());
                        if ($nodes !== null && \count($nodes)) {
                            $this->addLicenseToNode($nodes[0], $file);
                        }
                    } catch (\EbayVendor\PhpParser\Error $exception) {
                        $output->writeln('Syntax error on file ' . $file->getRelativePathname() . '. Continue ...');
                        $this->reporter->reportLicenseCouldNotBeFixed($file->getFilename());
                    }
                    break;
                case 'js':
                case 'css':
                case 'scss':
                    $this->addLicenseToFile($file);
                    break;
                case 'tpl':
                    $this->addLicenseToSmartyTemplate($file);
                    break;
                case 'twig':
                    $this->addLicenseToTwigTemplate($file);
                    break;
                case 'json':
                    $this->addLicenseToJsonFile($file);
                    break;
                case 'vue':
                    $this->addLicenseToHtmlFile($file);
                    break;
            }
            $progress->advance();
        }
        $progress->finish();
        $output->writeln('');
    }
    private function addLicenseToFile($file, $startDelimiter = '\\/', $endDelimiter = '\\/')
    {
        $content = $file->getContents();
        $oldContent = $content;
        // Regular expression found thanks to Stephen Ostermiller's Blog. http://blog.ostermiller.org/find-comment
        $regex = '%' . $startDelimiter . '\\*([^*]|[\\r\\n]|(\\*+([^*' . $endDelimiter . ']|[\\r\\n])))*\\*+' . $endDelimiter . '%';
        $matches = [];
        $text = $this->text;
        if ($startDelimiter != '\\/') {
            $text = $startDelimiter . \ltrim($text, '/');
        }
        if ($endDelimiter != '\\/') {
            $text = \rtrim($text, '/') . $endDelimiter;
        }
        // Try to find an existing license
        \preg_match($regex, $content, $matches);
        if (\count($matches)) {
            // Found - Replace it if prestashop one
            foreach ($matches as $match) {
                if (\stripos($match, $this->discriminationString) !== \false) {
                    $content = \str_replace($match, $text, $content);
                }
            }
        } else {
            // Not found - Add it at the beginning of the file
            $content = $text . "\n" . $content;
        }
        if (!$this->runAsDry) {
            \file_put_contents($this->targetDirectory . '/' . $file->getRelativePathname(), $content);
        }
        $this->reportOperationResult($content, $oldContent, $file->getFilename());
    }
    /**
     * @param \PhpParser\Node\Stmt $node
     */
    private function addLicenseToNode($node, SplFileInfo $file)
    {
        if (!$node->hasAttribute('comments')) {
            $needle = '<?php';
            $replace = "<?php\n" . $this->text . "\n";
            $haystack = $file->getContents();
            $pos = \strpos($haystack, $needle);
            // Important, if the <?php is in the middle of the file, continue
            if ($pos === 0) {
                $newstring = \substr_replace($haystack, $replace, $pos, \strlen($needle));
                if (!$this->runAsDry) {
                    \file_put_contents($this->targetDirectory . '/' . $file->getRelativePathname(), $newstring);
                }
                $this->reportOperationResult($newstring, $haystack, $file->getFilename());
            }
            return;
        }
        $comments = $node->getAttribute('comments');
        foreach ($comments as $comment) {
            if ($comment instanceof \EbayVendor\PhpParser\Comment && \strpos($comment->getText(), $this->discriminationString) !== \false) {
                $newContent = \str_replace($comment->getText(), $this->text, $file->getContents());
                if (!$this->runAsDry) {
                    \file_put_contents($this->targetDirectory . '/' . $file->getRelativePathname(), $newContent);
                }
                $this->reportOperationResult($newContent, $file->getContents(), $file->getFilename());
            }
        }
    }
    private function addLicenseToSmartyTemplate(SplFileInfo $file)
    {
        $this->addLicenseToFile($file, '{', '}');
    }
    private function addLicenseToTwigTemplate(SplFileInfo $file)
    {
        if (\strrpos($file->getRelativePathName(), 'html.twig') !== \false) {
            $this->addLicenseToFile($file, '{#', '#}');
        }
    }
    private function addLicenseToHtmlFile(SplFileInfo $file)
    {
        $this->addLicenseToFile($file, '<!--', '-->');
    }
    /**
     * @return bool
     */
    private function addLicenseToJsonFile(SplFileInfo $file)
    {
        if (!\in_array($file->getFilename(), ['composer.json', 'package.json'])) {
            return \false;
        }
        $content = (array) \json_decode($file->getContents());
        $oldContent = $content;
        $content['author'] = 'PrestaShop';
        $content['license'] = \false !== \strpos($this->license, 'afl') ? 'AFL-3.0' : 'OSL-3.0';
        if (!$this->runAsDry) {
            $result = \file_put_contents($this->targetDirectory . '/' . $file->getRelativePathname(), \json_encode($content, \JSON_UNESCAPED_SLASHES | \JSON_PRETTY_PRINT));
        } else {
            $result = \true;
        }
        $this->reportOperationResult($content, $oldContent, $file->getFilename());
        return \false !== $result;
    }
    private function reportOperationResult($newFileContent, $oldFileContent, $filename)
    {
        if ($newFileContent != $oldFileContent) {
            $this->reporter->reportLicenseHasBeenFixed($filename);
        } else {
            $this->reporter->reportLicenseWasFine($filename);
        }
    }
    private function printPrettyReport(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->section('Header Stamp Report');
        $report = $this->reporter->getReport();
        $sections = ['fixed', 'nothing to fix', 'failed'];
        foreach ($sections as $section) {
            if (empty($report[$section])) {
                continue;
            }
            $style->text(\ucfirst($section) . ':');
            $style->listing($report[$section]);
        }
    }
    private function printDryRunPrettyReport(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->section('Header Stamp Dry Run Report');
        $report = $this->reporter->getReport();
        if (empty($report['fixed'])) {
            return;
        }
        $style->text('Files with bad license headers:');
        $style->listing($report['fixed']);
    }
}
