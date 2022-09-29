<?php

/**
 * Tests that files are not executable.
 *
 * @author    Matthew Peveler <matt.peveler@gmail.com>
 * @copyright 2019 Matthew Peveler
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace EbayVendor\PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use EbayVendor\PHP_CodeSniffer\Files\File;
use EbayVendor\PHP_CodeSniffer\Sniffs\Sniff;
class ExecutableFileSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [\T_OPEN_TAG, \T_OPEN_TAG_WITH_ECHO];
    }
    //end register()
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $filename = $phpcsFile->getFilename();
        if ($filename !== 'STDIN') {
            $perms = \fileperms($phpcsFile->getFilename());
            if (($perms & 0x40) !== 0 || ($perms & 0x8) !== 0 || ($perms & 0x1) !== 0) {
                $error = 'A PHP file should not be executable; found file permissions set to %s';
                $data = [\substr(\sprintf('%o', $perms), -4)];
                $phpcsFile->addError($error, 0, 'Executable', $data);
            }
        }
        // Ignore the rest of the file.
        return $phpcsFile->numTokens + 1;
    }
    //end process()
}
//end class
