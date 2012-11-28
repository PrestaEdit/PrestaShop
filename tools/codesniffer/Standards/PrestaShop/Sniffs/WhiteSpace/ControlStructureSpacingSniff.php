<?php
/**
 * Check spaces in control structures (places with * are checked) : if*(*condition*) 
 *
 * @author Raphael Malie
 */
class Prestashop_Sniffs_WhiteSpace_ControlStructureSpacingSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return array(
                T_IF,
                T_WHILE,
                T_FOREACH,
                T_FOR,
                T_SWITCH,
                T_ELSEIF,
               );

    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

		// Check spaces before first parenthesis
        if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE)
        	$spaces = strlen($tokens[$stackPtr + 1]['content']);
        else
        	$spaces = 0;
        
        if ($spaces != 1)
        {
       		$error = '1 space expected after "%s"; %s found';
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterStructure', array(
            	$tokens[$stackPtr]['content'],
            	$spaces,
            ));
        }

        // Check spaces after first parenthesis
       	$spaces = 0;
       	if ($tokens[$tokens[$stackPtr]['parenthesis_opener'] + 1]['code'] === T_WHITESPACE)
       		$spaces = strlen($tokens[$tokens[$stackPtr]['parenthesis_opener'] + 1]['content']);
       		
        if ($spaces > 0)
        {
       		$error = '1 space expected after parenthesis "%s"; %s found';
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterStructure', array(
            	$tokens[$stackPtr]['content'].' ( <-',
            	$spaces,
            ));
        }
       		
       	// Check spaces before last parenthesis
       	$spaces = 0;
       	if ($tokens[$tokens[$stackPtr]['parenthesis_closer'] - 1]['code'] === T_WHITESPACE)
       		$spaces = strlen($tokens[$tokens[$stackPtr]['parenthesis_closer'] - 1]['content']);
       		
    	if ($spaces > 0)
        {
       		$error = '1 space expected before last parenthesis "%s"; %s found';
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterStructure', array(
            	$tokens[$stackPtr]['content'].' (... -> )',
            	$spaces,
            ));
        }
    }
}