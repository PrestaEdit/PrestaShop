<?php
/**
 * Squiz_Sniffs_Strings_ConcatenationSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ConcatenationSpacingSniff.php 8456 2011-09-09 15:56:52Z rMalie $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Strings_ConcatenationSpacingSniff.
 *
 * Makes sure there are no spaces between the concatenation operator (.) and
 * the strings being concatenated.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Prestashop_Sniffs_Strings_ConcatenationSpacingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING_CONCAT);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        
        $error = false;
        if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE)
        {
        	$previous = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        	if ($tokens[$stackPtr]['line'] == $tokens[$previous]['line'])
        		$error = true;
        }
        
		if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE)
        {
        	$next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        	if ($tokens[$stackPtr]['line'] == $tokens[$next]['line'])
        		$error = true;
        }
        
        if ($error)
        {
            $message = 'Concat operator must not be surrounded by spaces';
            $phpcsFile->addError($message, $stackPtr, 'Missing');
        }

    }//end process()


}//end class

?>
